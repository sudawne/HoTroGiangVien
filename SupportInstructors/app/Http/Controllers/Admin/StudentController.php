<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('class');

        // Lọc theo lớp
        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        // Tìm kiếm
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                    ->orWhere('student_code', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(15);
        $classes = Classes::all(); // Lấy danh sách lớp để fill vào dropdown lọc

        return view('admin.students.index', compact('students', 'classes'));
    }

    public function show(string $id)
    {
        // Eager Load: Lấy Sinh viên kèm theo User, Lớp, Người thân, Nợ môn
        $student = Student::with([
            'user',
            'class',
            'relatives',
            'debts' => function ($q) {
                $q->where('status', 'owed'); // Chỉ lấy môn đang nợ cho Tab Học vụ
            }
        ])->findOrFail($id);

        return view('admin.students.show', compact('student'));
    }

    // Các hàm create, store, edit... giữ nguyên hoặc code sau
    public function create()
    {
        return view('admin.students.create');
    }
    public function store(Request $request)
    {
        return redirect()->route('admin.students.index');
    }
    public function edit(string $id)
    {
        return view('admin.students.edit');
    }
    public function update(Request $request, string $id)
    {
        return redirect()->route('admin.students.index');
    }
    public function destroy(string $id)
    {
        return redirect()->route('admin.students.index');
    }
}
