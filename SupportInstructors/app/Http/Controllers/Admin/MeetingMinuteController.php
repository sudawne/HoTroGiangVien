<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\MeetingMinute;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Shared\Html;

class MeetingMinuteController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingMinute::with(['studentClass', 'semester', 'creator']);
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }
        if ($request->filled('academic_year')) {
            $query->whereHas('semester', function($q) use ($request) {
                $q->where('academic_year', $request->academic_year);
            });
        }

        // 3. Logic lọc theo Trạng thái
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $minutes = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // 4. Lấy dữ liệu cho Sidebar: Nhóm học kỳ theo năm học [cite: 5, 8]
        $academicYears = Semester::orderBy('academic_year', 'desc')
                            ->get()
                            ->groupBy('academic_year');

        return view('admin.minutes.index', compact('minutes', 'academicYears'));
    }

    /**
     * Hiển thị form tạo mới
     */
    public function create(Request $request)
    {
        // 1. Lấy tất cả các lớp
        $classes = Classes::all(); 

        // 2. Xác định lớp đang chọn
        $selectedClassId = $request->input('class_id', $classes->first()->id ?? null);
        
        // 3. Lấy thông tin lớp hiện tại kèm SV và Cố vấn
        $currentClass = $selectedClassId 
            ? Classes::with(['advisor.user', 'students'])->find($selectedClassId) 
            : null;

        // 4. Lấy danh sách SV (nếu có lớp)
        $students = $currentClass ? $currentClass->students : collect([]);
        
        // 5. Lấy học kỳ (Mới nhất lên đầu)
        $semesters = Semester::orderBy('start_date', 'desc')->get();

        return view('admin.minutes.create', compact('classes', 'currentClass', 'students', 'semesters'));
    }

    /**
     * Lưu biên bản vào CSDL
     */
    public function store(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'title' => 'required|string|max:255',
            'semester_id' => 'required|exists:semesters,id',
            'held_at' => 'required|date',
            'ended_at' => 'nullable|date|after:held_at', // Kết thúc phải sau bắt đầu
            'location' => 'required|string|max:255',
            
            // Validate Sinh viên (Có thể null nhưng nếu có phải tồn tại)
            'monitor_id' => 'nullable|exists:students,id|different:secretary_id', // Không được trùng Thư ký
            'secretary_id' => 'nullable|exists:students,id|different:monitor_id', // Không được trùng Chủ trì
            
            'absent_list' => 'nullable|array', // Phải là mảng
            'absent_list.*' => 'exists:students,id', // Từng phần tử trong mảng phải là ID sinh viên tồn tại
        ], [
            'monitor_id.different' => 'Lớp trưởng (Chủ trì) và Thư ký không được là cùng một người.',
            'secretary_id.different' => 'Thư ký và Lớp trưởng (Chủ trì) không được là cùng một người.',
            'ended_at.after' => 'Thời gian kết thúc phải diễn ra sau thời gian bắt đầu.',
        ]);

        try {
            // 2. Chuẩn bị dữ liệu
            $data = $request->all(); // Lấy hết dữ liệu từ form
            
            // Gán người tạo là User đang đăng nhập
            $data['created_by'] = Auth::id();
            
            // Xử lý trạng thái (Dựa vào nút bấm ở view: name="action" value="draft" hoặc "publish")
            $data['status'] = $request->input('action') === 'publish' ? 'published' : 'draft';

            // 3. Tính toán sĩ số tham dự
            // Tổng sinh viên của lớp đó
            $totalStudents = Student::where('class_id', $request->class_id)
                                    ->where('status', 'studying') // Chỉ đếm sinh viên đang học
                                    ->count();
            
            // Số lượng vắng (đếm mảng absent_list)
            $absentCount = count($request->absent_list ?? []);
            
            // Số lượng tham dự = Tổng - Vắng
            $data['attendees_count'] = $totalStudents - $absentCount;

            // 4. Tạo bản ghi mới
            // Lưu ý: absent_list tự động được Model cast sang JSON nhờ $casts = ['absent_list' => 'array']
            MeetingMinute::create($data);

            // 5. Thông báo và chuyển hướng
            $message = $data['status'] === 'published' 
                ? 'Đã xuất bản biên bản họp lớp thành công!' 
                : 'Đã lưu nháp biên bản thành công!';

            return redirect()->route('admin.minutes.index')->with('success', $message);

        } catch (\Exception $e) {
            // Log lỗi để debug nếu cần: \Log::error($e->getMessage());
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Xem chi tiết
     */
    public function show($id)
    {
        // Lấy dữ liệu kèm quan hệ để hiển thị tên lớp, sinh viên
        $minute = MeetingMinute::with(['studentClass', 'semester', 'creator', 'monitor', 'secretary'])->findOrFail($id);
        
        // Lấy danh sách SV vắng mặt từ mảng ID lưu trong absent_list
        $absentStudents = \App\Models\Student::whereIn('id', $minute->absent_list ?? [])->get();

        return view('admin.minutes.show', compact('minute', 'absentStudents'));
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $minute = MeetingMinute::findOrFail($id);
        
        // Nếu đã duyệt (published), không cho phép vào trang sửa
        if ($minute->status === 'published') {
            return redirect()->route('admin.minutes.index')->with('error', 'Biên bản đã duyệt không thể chỉnh sửa.');
        }
        
        $classes = Classes::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        $currentClass = Classes::with('students')->find($minute->class_id);
        $students = $currentClass ? $currentClass->students : collect([]);

        return view('admin.minutes.edit', compact('minute', 'classes', 'semesters', 'students', 'currentClass'));
    }
    public function approve($id)
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            return back()->with('error', 'Chỉ Admin mới có quyền duyệt biên bản.');
        }

        $minute = MeetingMinute::findOrFail($id);
        $minute->update(['status' => 'published']);

        return redirect()->route('admin.minutes.index')->with('success', 'Đã phê duyệt và công bố biên bản.');
    }

    /**
     * Hàm Từ Chối/Không Duyệt
     */
    public function reject($id)
    {
        if (!Auth::check() || Auth::user()->role_id != 1) {
            return back()->with('error', 'Bạn không có quyền thực hiện hành động này.');
        }

        $minute = MeetingMinute::findOrFail($id);
        // Khi không duyệt, ta giữ trạng thái 'draft' để giảng viên có thể sửa lại 
        $minute->update(['status' => 'draft']); 

        return redirect()->route('admin.minutes.index')->with('warning', 'Đã từ chối biên bản. Yêu cầu giảng viên chỉnh sửa lại.');
    }

    /**
     * Cập nhật dữ liệu
     */
    public function update(Request $request, $id)
    {
        $minute = MeetingMinute::findOrFail($id);
        $request->validate([
            'title' => 'required|string|max:255',
            'held_at' => 'required|date',
            'ended_at' => 'nullable|date|after:held_at',
            'monitor_id' => 'nullable|different:secretary_id',
            'secretary_id' => 'nullable|different:monitor_id',
        ]);

        // Logic tính toán lại số lượng
        $totalStudents = Student::where('class_id', $minute->class_id)->where('status', 'studying')->count();
        $absentCount = count($request->absent_list ?? []);
        
        $data = $request->all();
        $data['attendees_count'] = $totalStudents - $absentCount;
        $data['status'] = $request->input('action') === 'publish' ? 'published' : 'draft';

        $minute->update($data);

        return redirect()->route('admin.minutes.index')->with('success', 'Cập nhật biên bản thành công.');
    }

    /**
     * Xóa biên bản
     */
    public function destroy($id)
    {
        $minute = MeetingMinute::findOrFail($id);
        $minute->delete();

        return redirect()->route('admin.minutes.index')->with('success', 'Đã xóa biên bản.');
    }
    public function exportWord($id)
    {
        $minute = MeetingMinute::with(['studentClass.advisor.user', 'semester', 'creator', 'monitor', 'secretary'])->findOrFail($id);
        $absentStudents = \App\Models\Student::whereIn('id', $minute->absent_list ?? [])->get();

        // 1. Khởi tạo PHPWord
        $phpWord = new PhpWord();
        
        // Cấu hình mặc định: Font Times New Roman, Cỡ 13 (chuẩn hành chính)
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(13);

        // 2. Tạo Section (Trang giấy A4)
        $section = $phpWord->addSection([
            'paperSize' => 'A4',
            'marginTop' => 1134,    // ~2cm
            'marginLeft' => 1134,
            'marginRight' => 1134,
            'marginBottom' => 1134,
        ]);

        // 3. HEADER: QUỐC HIỆU & TIÊU NGỮ (Dùng bảng không viền để chia 2 bên)
        $headerTable = $section->addTable(['borderSize' => 0, 'cellMargin' => 0, 'width' => 100 * 50]);
        $headerTable->addRow();
        
        // Cột Trái: Trường/Khoa
        $leftCell = $headerTable->addCell(5000);
        $leftCell->addText('TRƯỜNG ĐẠI HỌC KIÊN GIANG', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $leftCell->addText('KHOA THÔNG TIN TRUYỀN THÔNG', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        // Kẻ chân (giả lập bằng gạch dưới ________)
        $leftCell->addText('__________________________', ['bold' => true], ['alignment' => 'center']);

        // Cột Phải: Quốc hiệu
        $rightCell = $headerTable->addCell(5000);
        $rightCell->addText('CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM', ['bold' => true, 'size' => 12], ['alignment' => 'center']);
        $rightCell->addText('Độc lập - Tự do - Hạnh phúc', ['bold' => true, 'size' => 13], ['alignment' => 'center']);
        $rightCell->addText('__________________________', ['bold' => true], ['alignment' => 'center']);

        $section->addTextBreak(1); // Xuống dòng

        // 4. TIÊU ĐỀ BIÊN BẢN
        $section->addText('BIÊN BẢN HỌP LỚP', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addText($minute->title, ['bold' => true, 'size' => 14], ['alignment' => 'center']);
        
        $hocKy = $minute->semester->name ?? '...';
        $namHoc = $minute->semester->academic_year ?? '...';
        $section->addText("Học kỳ: {$hocKy}   Năm học: {$namHoc}", ['italic' => true], ['alignment' => 'center']);
        $section->addTextBreak(1);

        // 5. MỤC I: THÔNG TIN CHUNG
        $section->addText('I. THỜI GIAN, ĐỊA ĐIỂM, THÀNH PHẦN THAM DỰ', ['bold' => true]);
        
        $ngayHop = $minute->held_at ? $minute->held_at->format('H \g\i\ờ i, \n\g\à\y d/m/Y') : '...';
        $section->addText("1. Thời gian: Bắt đầu lúc {$ngayHop}.");
        $section->addText("2. Địa điểm: " . $minute->location);
        
        $section->addText("3. Thành phần tham dự:");
        $section->addText("- Cố vấn học tập: " . ($minute->studentClass->advisor->user->name ?? '...'), [], ['indentation' => ['left' => 300]]);
        $section->addText("- Lớp trưởng (Chủ trì): " . ($minute->monitor->fullname ?? '...'), [], ['indentation' => ['left' => 300]]);
        $section->addText("- Thư ký: " . ($minute->secretary->fullname ?? '...'), [], ['indentation' => ['left' => 300]]);
        
        $siSo = "Tổng số: " . ($minute->attendees_count + count($minute->absent_list ?? [])) . 
                "; Có mặt: " . $minute->attendees_count . 
                "; Vắng: " . count($minute->absent_list ?? []);
        
        $section->addText("- " . $siSo, [], ['indentation' => ['left' => 300]]);
        
        // Liệt kê tên vắng
        if(count($absentStudents) > 0) {
            $tenVang = $absentStudents->pluck('fullname')->implode(', ');
            $section->addText("(Danh sách vắng: {$tenVang})", ['italic' => true], ['indentation' => ['left' => 600]]);
        }

        $section->addTextBreak(1);

        // 6. NỘI DUNG CHÍNH (Xử lý HTML từ CKEditor)
        $section->addText('II. NỘI DUNG', ['bold' => true]);
        // Dùng hàm addHtml để convert thẻ <p>, <b> từ CKEditor sang Word
        Html::addHtml($section, $minute->content_discussions);

        $section->addTextBreak(1);

        // 7. KẾT LUẬN
        $section->addText('III. KẾT LUẬN', ['bold' => true]);
        Html::addHtml($section, $minute->content_conclusion);

        $section->addTextBreak(1);

        // 8. KIẾN NGHỊ
        $section->addText('IV. KIẾN NGHỊ', ['bold' => true]);
        Html::addHtml($section, $minute->content_requests);

        $section->addTextBreak(2);

        // 9. CHỮ KÝ (Bảng 2 cột)
        $footerTable = $section->addTable(['borderSize' => 0, 'width' => 100 * 50]);
        $footerTable->addRow();
        
        $c1 = $footerTable->addCell(5000);
        $c1->addText('THƯ KÝ', ['bold' => true], ['alignment' => 'center']);
        $c1->addTextBreak(3); // Khoảng trống ký tên
        $c1->addText($minute->secretary->fullname ?? '', ['bold' => true], ['alignment' => 'center']);

        $c2 = $footerTable->addCell(5000);
        $c2->addText('CỐ VẤN HỌC TẬP', ['bold' => true], ['alignment' => 'center']);
        $c2->addTextBreak(3);
        $c2->addText($minute->studentClass->advisor->user->name ?? '', ['bold' => true], ['alignment' => 'center']);

        // 10. Xuất file
        $filename = "Bien-ban-hop-lop-" . ($minute->studentClass->code ?? 'Lop') . "-" . date('d-m-Y') . ".docx";
        
        // Save to temp
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        
        return response()->streamDownload(function () use ($objWriter) {
            $objWriter->save('php://output');
        }, $filename);
    }
}