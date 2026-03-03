<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classes;
use App\Models\MeetingMinute;

class MeetingMinuteController extends Controller
{
    public function index()
    {
        return view('admin.minutes.index');
    }

    public function create(Request $request)
    {
        // 1. Lấy tất cả các lớp (để hiển thị vào dropdown)
        $classes = \App\Models\Classes::all(); // Hoặc lọc theo giảng viên: ->where('advisor_id', auth()->id())

        // 2. Xác định lớp đang chọn (Ưu tiên lấy từ URL ?class_id=..., nếu không có thì lấy lớp đầu tiên)
        $selectedClassId = $request->input('class_id', $classes->first()->id ?? null);
        
        // 3. Lấy thông tin lớp hiện tại và danh sách sinh viên của lớp đó
        $currentClass = \App\Models\Classes::with(['advisor.user', 'students'])->find($selectedClassId);

        // Xử lý trường hợp chưa có lớp nào
        $students = $currentClass ? $currentClass->students : collect([]);
        
        $semesters = \App\Models\Semester::orderBy('start_date', 'desc')->get();

        return view('admin.minutes.create', compact('classes', 'currentClass', 'students', 'semesters'));
    }

    public function store(Request $request)
    {
        // Validate và lưu dữ liệu
        // $minute = MeetingMinute::create($request->all());
        return redirect()->route('admin.minutes.index')->with('success', 'Đã tạo biên bản thành công');
    }

    // ... Các hàm khác
    public function show(string $id)
    {
        return view('admin.minutes.show');
    }
    public function edit(string $id)
    {
        return view('admin.minutes.edit');
    }
    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.minutes.index');
    }
    public function destroy(string $id)
    {
        return redirect()->route('admin.minutes.index');
    }
}
