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
    /**
     * Hiển thị danh sách biên bản
     */
    public function index()
    {
        // Lấy danh sách biên bản, sắp xếp mới nhất lên đầu
        // Dùng 'with' để load trước quan hệ, tránh lỗi N+1 query
        $minutes = MeetingMinute::with(['studentClass', 'semester', 'creator'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(10);

        return view('admin.minutes.index', compact('minutes'));
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
        $minute = MeetingMinute::with(['studentClass', 'semester', 'monitor', 'secretary'])->findOrFail($id);
        
        // Lấy danh sách chi tiết sinh viên vắng (từ mảng ID trong absent_list)
        $absentStudents = [];
        if (!empty($minute->absent_list)) {
            $absentStudents = Student::whereIn('id', $minute->absent_list)->get();
        }

        return view('admin.minutes.show', compact('minute', 'absentStudents'));
    }

    /**
     * Form chỉnh sửa
     */
    public function edit($id)
    {
        $minute = MeetingMinute::findOrFail($id);
        
        // Load lại các danh sách cần thiết giống hàm create
        $classes = Classes::all();
        $semesters = Semester::orderBy('start_date', 'desc')->get();
        
        $currentClass = Classes::with(['advisor.user', 'students'])->find($minute->class_id);
        $students = $currentClass ? $currentClass->students : collect([]);

        return view('admin.minutes.edit', compact('minute', 'classes', 'semesters', 'currentClass', 'students'));
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