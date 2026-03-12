<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AIController extends Controller
{
    public function askAI(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if (!$user) {
                return response()->json(['reply' => 'Dạ, Thầy/Cô vui lòng đăng nhập để sử dụng EduBot ạ.']);
            }

            $userMessage = trim((string) $request->input('message'));
            if ($userMessage === '') {
                return response()->json(['reply' => 'Dạ, Thầy/Cô cần em hỗ trợ gì ạ?']);
            }

            $apiKey = env('GROQ_API_KEY');
            if (empty($apiKey)) {
                return response()->json(['reply' => 'Hệ thống chưa được cấu hình GROQ_API_KEY trong file .env.']);
            }

            $lowerMsg = mb_strtolower($userMessage, 'UTF-8');
            if (preg_match('/^(hi|hello|chào|chao|alo|ê|hey)/', $lowerMsg)) {
                return response()->json(['reply' => 'Dạ em chào Thầy/Cô ạ! Em là EduBot. Thầy/Cô cần tra cứu thông tin sinh viên hay lớp học nào ạ?']);
            }

            $part1 = "https://";
            $part2 = "api.groq.com";
            $part3 = "/openai/v1/chat/completions";
            $apiUrl = $part1 . $part2 . $part3;

            $schema = "SCHEMA:\n"
                . "1. users(id, name, email, role_id, last_active_at)\n"
                . "2. lecturers(id, user_id, department_id, lecturer_code, degree)\n"
                . "3. classes(id, department_id, advisor_id, code, name, academic_year)\n"
                . "4. students(id, user_id, class_id, student_code, fullname, dob, status)\n"
                . "5. academic_warnings(id, student_id, semester_id, warning_level, credits_owed, reason, status)\n"
                . "RELATIONSHIPS:\n"
                . "students.class_id = classes.id\n"
                . "classes.advisor_id = lecturers.id\n"
                . "lecturers.user_id = users.id\n"
                . "academic_warnings.student_id = students.id\n";

            $sqlPrompt = "You are a highly restricted MySQL engine.\n"
                . "Schema:\n$schema\n\n"
                . "User asking: '$userMessage'\n\n"
                . "RULES:\n"
                . "1. Output ONLY ONE basic SQL SELECT statement to find the requested info.\n"
                . "2. Use LIKE '%keyword%' for text search.\n"
                . "3. NEVER use UNION.\n"
                . "4. Output NOTHING ELSE. No markdown.";

            $sqlResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($apiKey),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($apiUrl, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $sqlPrompt]],
                'temperature' => 0.0
            ]);

            if (!$sqlResponse->successful()) {
                return response()->json(['reply' => 'Dạ, lỗi kết nối tạo truy vấn: ' . $sqlResponse->status()]);
            }

            $sqlQuery = trim($sqlResponse->json()['choices'][0]['message']['content'] ?? '');
            $sqlQuery = str_replace(['```sql', '```', '`'], '', $sqlQuery);
            $sqlQuery = trim($sqlQuery);

            $dbData = [];

            if (preg_match('/^\s*SELECT/i', $sqlQuery) && !preg_match('/DROP|DELETE|UPDATE|INSERT|ALTER/i', $sqlQuery)) {
                try {
                    $dbData = DB::select($sqlQuery);
                    if (empty($dbData)) {
                        $dbData = ["info" => "Không có dữ liệu nào khớp với yêu cầu trên hệ thống."];
                    }
                } catch (\Exception $e) {
                    $dbData = ["info" => "Câu hỏi phức tạp, hệ thống chưa tra cứu được. Hãy báo người dùng cung cấp thêm thông tin."];
                }
            } else {
                $dbData = ["info" => "Lệnh tra cứu không hợp lệ."];
            }

            $finalPrompt = "Bạn là EduBot - Trợ lý Cố vấn học tập (nhóm NCKH Tăng Nhật Phát & Trần Tô Khánh Nguyên).\n"
                . "Câu hỏi: '$userMessage'\n"
                . "Dữ liệu tra cứu được:\n" . json_encode($dbData, JSON_UNESCAPED_UNICODE) . "\n\n"
                . "QUY TẮC:\n"
                . "1. Trả lời tiếng Việt, xưng 'dạ thưa', 'Thầy/Cô'.\n"
                . "2. TUYỆT ĐỐI KHÔNG nói các từ: 'JSON', 'SQL', 'Database', 'Cơ sở dữ liệu'. Hãy nói 'Dạ em kiểm tra hệ thống thấy...'\n"
                . "3. In đậm thông tin quan trọng.\n"
                . "4. Trình bày thông tin theo dạng gạch đầu dòng cho dễ đọc.";

            $finalResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . trim($apiKey),
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($apiUrl, [
                'model' => 'llama-3.3-70b-versatile',
                'messages' => [['role' => 'user', 'content' => $finalPrompt]],
                'temperature' => 0.3
            ]);

            if ($finalResponse->successful()) {
                $aiText = $finalResponse->json()['choices'][0]['message']['content'] ?? 'Dạ em chưa hiểu ạ.';
                $aiText = preg_replace('/\*\*(.*?)\*\*/', '<b>$1</b>', $aiText);
                $aiText = preg_replace('/^\* (.*)$/m', '• $1', $aiText);
                return response()->json(['reply' => nl2br($aiText)]);
            }

            return response()->json(['reply' => 'Dạ, hệ thống đang bận. Lỗi lần 2: ' . $finalResponse->status()]);
        } catch (\Exception $e) {
            return response()->json(['reply' => "🛑 Lỗi hệ thống: " . $e->getMessage()]);
        }
    }
}
