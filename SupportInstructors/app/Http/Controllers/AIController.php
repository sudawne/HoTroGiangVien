<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use App\Services\EduQueryService;

class AIController extends Controller
{
    protected $eduService;
    protected int $maxAttempts = 30;
    protected int $decaySeconds = 60;

    public function __construct(EduQueryService $eduService)
    {
        $this->eduService = $eduService;
    }

    public function askAI(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return response()->json(['reply' => 'Dạ, Thầy/Cô vui lòng đăng nhập ạ.'], 401);
        }

        $roleName = $user->role->name ?? null;
        if (!in_array($roleName, ['ADMIN', 'LECTURER'])) {
            return response()->json(['reply' => '🛑 Dạ thưa, tính năng này chỉ dành cho Giảng viên / Quản trị viên.'], 403);
        }

        // rate limit
        $key = 'ai-ask:' . $user->id;
        if (RateLimiter::tooManyAttempts($key, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json(['reply' => "Vui lòng chờ {$seconds} giây trước khi gọi lại."], 429);
        }
        RateLimiter::hit($key, $this->decaySeconds);

        $q = trim((string)$request->input('query', ''));
        $intent = $request->input('intent', 'auto');
        $debug = $request->boolean('debug', false);

        // greeting
        if (preg_match('/^(hi|hello|chào|chao|xin chào)$/i', $q)) {
            return response()->json(['reply' => 'GREETING']);
        }

        // intent auto-detect (rule-based)
        if ($intent === 'auto') {
            $intent = $this->detectIntent($q);
        }

        // Build a short system prompt instructing LLM to return EXACT JSON spec
        $specPrompt = $this->buildSpecPrompt($q, $intent);

        // call LLM to create spec
        $apiKey = env('GROQ_API_KEY');
        $apiUrl = env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');

        $spec = null;
        if (!empty($apiKey)) {
            try {
                $resp = Http::withHeaders([
                    'Authorization' => 'Bearer ' . trim($apiKey),
                    'Content-Type' => 'application/json',
                ])->timeout(20)->post($apiUrl, [
                    'model' => 'llama-3.3-70b-versatile',
                    'messages' => [['role' => 'user', 'content' => $specPrompt]],
                    'temperature' => 0.0,
                    'max_tokens' => 600
                ]);

                if ($resp->successful()) {
                    $content = $resp->json()['choices'][0]['message']['content'] ?? '';
                    // strip markdown fences if any
                    $content = preg_replace('/^```(?:json)?\s+/', '', $content);
                    $content = preg_replace('/\s+```$/', '', $content);
                    $content = trim($content);

                    // try decode
                    $decoded = json_decode($content, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $spec = $decoded;
                    } else {
                        Log::warning('EduBot: invalid JSON spec from LLM', ['raw' => $content, 'err' => json_last_error_msg()]);
                    }
                } else {
                    Log::warning('EduBot LLM spec call failed', ['status' => $resp->status(), 'body' => $resp->body()]);
                }
            } catch (\Throwable $e) {
                Log::error('EduBot LLM spec exception: ' . $e->getMessage());
            }
        } else {
            Log::info('EduBot: no API key configured, skipping LLM spec generation.');
        }

        // If spec invalid or absent, fallback to deterministic spec based on intent+q
        if (! is_array($spec)) {
            $spec = $this->fallbackSpecFromIntent($intent, $q);
        }

        // Debug: show spec when debug true
        if ($debug) {
            return response()->json(['reply' => 'SPEC_DEBUG', 'spec' => $spec]);
        }

        // Execute spec safely
        try {
            $resultData = $this->eduService->executeQuerySpec($spec);
        } catch (\Throwable $e) {
            Log::error('EduBot executeQuerySpec error: ' . $e->getMessage(), ['spec' => $spec]);
            // fallback to previous behavior: try direct name search
            $resultData = $this->eduService->timSinhVienTheoTenHoacMSSV($q, 30);
        }

        // redact if no permission
        $canViewSensitive = in_array($roleName, ['ADMIN', 'LECTURER']);
        $resultData = $this->eduService->redactIfNoPermission($resultData, $canViewSensitive);

        // Audit
        Log::info('EduBot Query executed', [
            'user_id' => $user->id,
            'role' => $roleName,
            'query' => $q,
            'intent' => $intent,
            'spec' => $spec,
            'result_count' => count($resultData),
        ]);

        if (empty($resultData)) {
            return response()->json(['reply' => "Dạ thưa Thầy/Cô, em đã tra nhưng hệ thống không tìm thấy hồ sơ phù hợp với \"{$q}\"."]);
        }

        // render human friendly (LLM or local summary)
        $humanText = $this->callLLMToRender($q, $resultData, $canViewSensitive);

        return response()->json(['reply' => $humanText]);
    }

    protected function buildSpecPrompt(string $q, string $intent): string
    {
        // Strict instruction: output EXACT JSON only, matching schema.
        $prompt = "You are a JSON generator. Given a user's short Vietnamese query and an intent, OUTPUT EXACTLY a JSON object matching the schema below (NO extra text, NO SQL, NO explanation).\n\n";
        $prompt .= "SCHEMA:\n";
        $prompt .= "{\n";
        $prompt .= "  \"entity\": \"<one of: students, academic_warnings, academic_results, consultation_logs>\",\n";
        $prompt .= "  \"filters\": { \"<filter_key>\": \"<value>\", ... },\n";
        $prompt .= "  \"fields\": [\"field1\",\"field2\",...],\n";
        $prompt .= "  \"limit\": <integer between 1 and 200>,\n";
        $prompt .= "  \"order_by\": {\"column\":\"<field>\",\"direction\":\"asc|desc\"}\n";
        $prompt .= "}\n\n";
        $prompt .= "RULES:\n";
        $prompt .= "1) entity must be one allowed in schema.\n";
        $prompt .= "2) filters keys must be simple (examples: fullname_like, student_code, student_code, semester_id, warning_level, topic_like).\n";
        $prompt .= "3) fields must be among the entity's fields (e.g. for students: student_code, fullname, dob, status, class_code, class_name, email).\n";
        $prompt .= "4) DO NOT output null or empty filters; omit keys if not used.\n";
        $prompt .= "5) Output valid JSON only. Use double quotes. No markdown fences. No comments.\n\n";
        $prompt .= "USER QUERY (Vietnamese): \"" . addslashes($q) . "\"\n";
        $prompt .= "INTENT: \"" . addslashes($intent) . "\"\n\n";
        $prompt .= "Produce the JSON spec now.\n";
        return $prompt;
    }

    protected function fallbackSpecFromIntent(string $intent, string $q): array
    {
        $qTrim = trim($q);
        if ($intent === 'canhbao') {
            return [
                'entity' => 'academic_warnings',
                'filters' => $this->guessFiltersFromQuery($qTrim),
                'fields' => ['warning_id', 'student_code', 'fullname', 'semester', 'warning_level', 'reason', 'status'],
                'limit' => 50,
            ];
        }
        if ($intent === 'ketqua') {
            return [
                'entity' => 'academic_results',
                'filters' => $this->guessFiltersFromQuery($qTrim),
                'fields' => ['result_id', 'student_code', 'fullname', 'semester', 'gpa_10', 'gpa_4', 'classification'],
                'limit' => 20,
            ];
        }
        if ($intent === 'lichsu_tuvan') {
            return [
                'entity' => 'consultation_logs',
                'filters' => $this->guessFiltersFromQuery($qTrim),
                'fields' => ['log_id', 'student_code', 'fullname', 'meeting_date', 'topic', 'advisor_name'],
                'limit' => 30,
            ];
        }
        // default students
        return [
            'entity' => 'students',
            'filters' => $this->guessFiltersFromQuery($qTrim),
            'fields' => ['student_code', 'fullname', 'class_name', 'status', 'email'],
            'limit' => 30,
        ];
    }

    protected function guessFiltersFromQuery(string $q): array
    {
        // simple heuristics: if query contains digits -> treat as student_code, else fullname_like
        if (preg_match('/[0-9]/', $q) && preg_match('/[A-Za-z0-9\-]{3,}/', $q)) {
            return ['student_code' => $q];
        }
        if ($q !== '') {
            return ['fullname_like' => $q];
        }
        return [];
    }

    // (previous detectIntent and callLLMToRender/buildLocalSummary methods remain the same as in prior version)
    protected function detectIntent(string $q): string
    {
        $lower = mb_strtolower($q);
        $warningKeys = ['cảnh báo', 'canhbao', 'nợ', 'nợ môn', 'rớt'];
        foreach ($warningKeys as $k) {
            if (str_contains($lower, $k)) return 'canhbao';
        }
        $resultKeys = ['điểm', 'gpa', 'đtb', 'kết quả', 'xếp loại'];
        foreach ($resultKeys as $k) {
            if (str_contains($lower, $k)) return 'ketqua';
        }
        $consultKeys = ['tư vấn', 'tu van', 'gặp', 'lịch sử tư vấn', 'consult'];
        foreach ($consultKeys as $k) {
            if (str_contains($lower, $k)) return 'lichsu_tuvan';
        }
        if (preg_match('/^[A-Za-z0-9\-]{4,20}$/', $q)) return 'tim_sinhvien';
        return 'tim_sinhvien';
    }

    // callLLMToRender and buildLocalSummary: reuse previous implementations (copy from prior file)
    protected function callLLMToRender(string $userMessage, array $data, bool $canViewSensitive): string
    {
        // (same as previous implementation in the last version)
        $apiKey = env('GROQ_API_KEY');
        if (empty($apiKey)) {
            return $this->buildLocalSummary($userMessage, $data, $canViewSensitive);
        }

        $slice = array_slice($data, 0, 20);
        $prompt = "Bạn là 'EduBot' - trợ lý cố vấn học tập. Thầy/Cô hỏi: \"{$userMessage}\".\n";
        $prompt .= "Dưới đây là dữ liệu đã truy xuất (JSON). Hãy viết câu trả lời tiếng Việt lịch sự, xưng hô 'Dạ thưa Thầy/Cô', in đậm phần quan trọng bằng tag <b>...</b>. KHÔNG dùng các từ: 'SQL', 'database', 'trích xuất'. Trả về HTML-friendly text.\n\n";
        $prompt .= json_encode($slice, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $prompt .= "\n\nQUY TẮC: Không trả JSON, không thêm mã SQL, tối đa ~300 từ.";

        try {
            $apiUrl = env('GROQ_API_URL', 'https://api.groq.com/openai/v1/chat/completions');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($apiKey),
                'Content-Type' => 'application/json',
            ])->timeout(25)->post($apiUrl, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $prompt]],
                'temperature' => 0.2,
                'max_tokens' => 800
            ]);

            if (!$response->successful()) {
                Log::warning('EduBot LLM render failed', ['status' => $response->status(), 'body' => $response->body()]);
                return $this->buildLocalSummary($userMessage, $data, $canViewSensitive);
            }

            $json = $response->json();
            $aiText = $json['choices'][0]['message']['content'] ?? null;
            if (empty($aiText)) {
                return $this->buildLocalSummary($userMessage, $data, $canViewSensitive);
            }
            $aiText = preg_replace('/\b(SQL|Database|trích xuất)\b/i', '***', $aiText);
            $aiText .= "<br><br><small style='color:#ccc;'><i>[Source]: EduQueryService</i></small>";
            return $aiText;
        } catch (\Throwable $e) {
            Log::error('EduBot LLM render exception: ' . $e->getMessage());
            return $this->buildLocalSummary($userMessage, $data, $canViewSensitive);
        }
    }

    protected function buildLocalSummary(string $userMessage, array $data, bool $canViewSensitive): string
    {
        $count = count($data);
        $html = "Dạ thưa Thầy/Cô, em đã tra giúp: <b>{$count} kết quả</b> phù hợp với yêu cầu \"<i>{$userMessage}</i>\".<br>";
        $html .= "<ul style='margin:6px 0;padding-left:18px;'>";
        $maxShow = 10;
        $i = 0;
        foreach ($data as $row) {
            if ($i++ >= $maxShow) {
                $remaining = $count - $maxShow;
                if ($remaining > 0) $html .= "<li>... và {$remaining} kết quả khác.</li>";
                break;
            }
            $label = $row['student_code'] ?? ($row['warning_id'] ?? ($row['result_id'] ?? '—'));
            $name = $row['fullname'] ?? '—';
            $extra = '';
            if (isset($row['class_name']) && $row['class_name']) $extra = " — Lớp: {$row['class_name']}";
            $email = $row['email'] ?? null;
            $emailText = $canViewSensitive && $email ? " (Email: {$email})" : '';
            $html .= "<li><b>{$label}</b>: {$name}{$extra}{$emailText}</li>";
        }
        $html .= "</ul>";
        $html .= "<small style='color:#ccc;'><i>[Source]: EduQueryService</i></small>";
        return $html;
    }
}
