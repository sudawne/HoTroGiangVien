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

    public function create()
    {
        // Cần truyền danh sách Lớp xuống View để hiển thị trong Dropdown
        $classes = Classes::all();

        // Truyền thêm học kỳ nếu có bảng Semesters (tạm thời hardcode ở view hoặc thêm model sau)
        return view('admin.minutes.create', compact('classes'));
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
