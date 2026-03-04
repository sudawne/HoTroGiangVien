<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\MeetingMinute;
use App\Models\Student;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;

class MeetingMinuteController extends Controller
{
    public function index(Request $request)
    {
        $query = MeetingMinute::with(['studentClass', 'semester', 'creator']);

        // 1. Logic lọc theo Học kỳ (nếu có)
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->semester_id);
        }

        // 2. Logic lọc theo Năm học (nếu có)
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
        // Kiểm tra quyền Admin (role_id = 1) 
        if (!Auth::check() || Auth::user()->role_id != 1) {
            return back()->with('error', 'Chỉ Admin mới có quyền duyệt biên bản.');
        }

        $minute = MeetingMinute::findOrFail($id);
        $minute->update(['status' => 'published']); // Chuyển sang trạng thái đã công bố 

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

        // Validate tương tự store
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
}