<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\MeetingMinute;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

// --- KHAI BÁO THƯ VIỆN ---
use Illuminate\Support\Str;                 // Xử lý chuỗi
use PhpOffice\PhpWord\PhpWord;              // Class chính tạo file Word
use PhpOffice\PhpWord\IOFactory;            // Class xuất file
use PhpOffice\PhpWord\Shared\Html;          // Class xử lý HTML
// Không cần use TblWidth nữa để tránh lỗi
// --------------------------

class MeetingMinuteController extends Controller
{
    // --- 1. INDEX ---
    public function index(Request $request) {
        $query = MeetingMinute::with(['studentClass', 'semester', 'creator']);
        if ($request->filled('semester_id')) $query->where('semester_id', $request->semester_id);
        if ($request->filled('academic_year')) {
            $query->whereHas('semester', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }
        if ($request->filled('status')) $query->where('status', $request->status);
        
        $minutes = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        $academicYears = Semester::orderBy('academic_year', 'desc')->get()->groupBy('academic_year');
        
        return view('admin.minutes.index', compact('minutes', 'academicYears'));
    }

    // --- 2. CREATE ---
    public function create(Request $request) {
        $classes = Classes::all();
        $selectedClassId = $request->input('class_id', $classes->first()->id ?? null);
        $currentClass = $selectedClassId ? Classes::with(['advisor.user', 'students'])->find($selectedClassId) : null;
        $students = $currentClass ? $currentClass->students : collect([]);
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        return view('admin.minutes.create', compact('classes', 'currentClass', 'students', 'semesters'));
    }

    // --- 3. STORE ---
    public function store(Request $request) {
        $request->validate(['title' => 'required', 'class_id' => 'required']);
        $data = $request->all();
        $data['created_by'] = Auth::id();
        $data['status'] = $request->input('action') === 'publish' ? 'published' : 'draft';
        
        $total = Student::where('class_id', $request->class_id)->count();
        $data['attendees_count'] = $total - count($request->absent_list ?? []);
        
        MeetingMinute::create($data);
        return redirect()->route('admin.minutes.index')->with('success', 'Lưu thành công');
    }

    // --- 4. SHOW ---
    public function show($id) {
        $minute = MeetingMinute::with(['studentClass', 'semester', 'creator', 'monitor', 'secretary'])->findOrFail($id);
        $absentStudents = \App\Models\Student::whereIn('id', $minute->absent_list ?? [])->get();
        return view('admin.minutes.show', compact('minute', 'absentStudents'));
    }

    // --- 5. EDIT ---
    public function edit($id) {
        $minute = MeetingMinute::findOrFail($id);
        if($minute->status === 'published') return redirect()->route('admin.minutes.index')->with('error', 'Đã duyệt không thể sửa');
        
        $classes = Classes::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $currentClass = Classes::with('students')->find($minute->class_id);
        $students = $currentClass ? $currentClass->students : collect([]);
        
        return view('admin.minutes.edit', compact('minute', 'classes', 'semesters', 'students', 'currentClass'));
    }

    // --- 6. UPDATE ---
    public function update(Request $request, $id) {
        $minute = MeetingMinute::findOrFail($id);
        $data = $request->all();
        
        $total = Student::where('class_id', $minute->class_id)->count();
        $data['attendees_count'] = $total - count($request->absent_list ?? []);
        $data['status'] = $request->input('action') === 'publish' ? 'published' : 'draft';
        
        $minute->update($data);
        return redirect()->route('admin.minutes.index')->with('success', 'Cập nhật thành công');
    }

    // --- 7. APPROVE ---
    public function approve($id) {
        if((Auth::user()->role_id ?? 0) != 1) return back();
        MeetingMinute::where('id', $id)->update(['status' => 'published']);
        return redirect()->route('admin.minutes.index')->with('success', 'Đã duyệt.');
    }

    // --- 9. DESTROY ---
    public function destroy($id) {
        MeetingMinute::destroy($id);
        return back()->with('success', 'Đã xóa.');
    }

    // --- 10. EXPORT WORD (ĐÃ FIX LỖI PCT) ---
    public function exportWord($id)
    {
        $minute = MeetingMinute::with(['studentClass.advisor.user', 'semester', 'creator', 'monitor', 'secretary'])->findOrFail($id);
        $absentStudents = \App\Models\Student::whereIn('id', $minute->absent_list ?? [])->get();

        // Khởi tạo
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);

        // Tạo trang A4
        $section = $phpWord->addSection([
            'paperSize' => 'A4',
            'marginTop' => 1134, 'marginBottom' => 1134,
            'marginLeft' => 1701, 'marginRight' => 1134,
        ]);

        $table = $section->addTable(['unit' => 'pct', 'width' => 5000]); 
        $table->addRow();

        $headerStyle = ['alignment' => 'center', 'spaceAfter' => 0];
        $boldStyle = ['bold' => true, 'size' => 11];
        $Style = ['size' => 11];
        
        // Cột Trái
        $cellLeft = $table->addCell(4500); // 45%
        $cellLeft->addText('TRƯỜNG ĐẠI HỌC KIÊN GIANG', $Style, $headerStyle);
        $cellLeft->addText('KHOA THÔNG TIN TRUYỀN THÔNG', $boldStyle, $headerStyle);
        $cellLeft->addText('_______________________', $boldStyle, $headerStyle);

        // Cột Phải
        $cellRight = $table->addCell(5500); // 55%
        $cellRight->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', $boldStyle, $headerStyle);
        $cellRight->addText('Độc lập - Tự do - Hạnh phúc', $boldStyle, $headerStyle);
        $cellRight->addText('_______________________', $boldStyle, $headerStyle);

        $section->addTextBreak(1);

        // Tiêu đề
        $section->addText('BIÊN BẢN HỌP LỚP', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText($minute->title, ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        $section->addText(
            "Học kỳ: " . ($minute->semester->name ?? '...') . " Năm học: " . ($minute->semester->academic_year ?? '...'),
            ['italic' => false], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Hàm hỗ trợ add heading
        $addHeading = function($text) use ($section) {
            $section->addText($text, ['bold' => true, 'size' => 13], ['spaceBefore' => 120, 'spaceAfter' => 0]);
        };

        // Mục I
        $addHeading('I. THỜI GIAN, ĐỊA ĐIỂM, THÀNH PHẦN THAM DỰ');
        $textRun1 = $section->addTextRun(['alignment' => 'both', 'spaceAfter' => 60]);
        $textRun1->addText("1. Thời gian: ", ['bold' => true]);
        $timeStr = $minute->held_at ? $minute->held_at->format('H:i \n\g\à\y d/m/Y') : '...';
        $textRun1->addText($timeStr);

        // 2. Địa điểm (Dùng TextRun tương tự)
        $textRun2 = $section->addTextRun(['alignment' => 'both', 'spaceAfter' => 60]);
        $textRun2->addText("2. Địa điểm: ", ['bold' => true]);
        $textRun2->addText($minute->location);

        $section->addText("3. Thành phần tham dự", ['bold' => true, 'spaceBefore' => 120]);
        $section->addText("- Cố vấn: " . ($minute->studentClass->advisor->user->name ?? ''), [], ['indentation' => ['left' => 300]]);
        $section->addText("- Chủ trì: " . ($minute->monitor->fullname ?? ''), [], ['indentation' => ['left' => 300]]);
        $section->addText("- Thư ký: " . ($minute->secretary->fullname ?? ''), [], ['indentation' => ['left' => 300]]);
        
        $siSoText = "- Tổng số: " . ($minute->attendees_count + count($minute->absent_list ?? [])) . 
                    "; Có mặt: " . $minute->attendees_count . 
                    "; Vắng: " . count($minute->absent_list ?? []);
        $section->addText($siSoText, [], ['indentation' => ['left' => 300]]);

        // Mục II, III, IV
        $addHeading('II. NỘI DUNG');
        Html::addHtml($section, $minute->content_discussions);

        $addHeading('III. KẾT LUẬN');
        Html::addHtml($section, $minute->content_conclusion);

        $addHeading('IV. KIẾN NGHỊ');
        Html::addHtml($section, $minute->content_requests);
        $endStr = $minute->ended_at ? $minute->ended_at->format('H:i') : '...';
        $section->addText("Cuộc họp kết thúc lúc vào lúc {$endStr} cùng ngày./.", ['italic' => false]);

        // Chữ ký (Sử dụng 'pct' cho bảng chữ ký luôn)
        $footerTable = $section->addTable(['unit' => 'pct', 'width' => 5000]);
        $footerTable->addRow();
        
        $footerTable->addCell(5000)->addText('THƯ KÝ', ['bold' => true], ['alignment' => 'center']);
        $footerTable->addCell(5000)->addText('CỐ VẤN HỌC TẬP', ['bold' => true], ['alignment' => 'center']);
        
        $footerTable->addRow();
        $footerTable->addCell(5000)->addTextBreak(3);
        $footerTable->addCell(5000)->addTextBreak(3);

        $footerTable->addRow();
        $footerTable->addCell(5000)->addText($minute->secretary->fullname ?? '', ['bold' => true], ['alignment' => 'center']);
        $footerTable->addCell(5000)->addText($minute->studentClass->advisor->user->name ?? '', ['bold' => true], ['alignment' => 'center']);

        // Xuất file
        $filename = "Bien-ban-" . Str::slug($minute->title) . ".docx";
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        
        return response()->streamDownload(function () use ($objWriter) {
            $objWriter->save('php://output');
        }, $filename);
    }
}