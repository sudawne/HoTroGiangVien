<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\User;

class SearchController extends Controller
{
    public function globalSearch(Request $request)
    {
        $query = trim($request->input('q'));

        // Nếu từ khóa quá ngắn thì trả về rỗng
        if (strlen($query) < 2) {
            return response()->json(['students' => [], 'lecturers' => []]);
        }

        // 1. TÌM SINH VIÊN (Trong bảng students)
        // Tìm theo: Họ tên hoặc MSSV
        $students = Student::with('studentClass') // Eager load để lấy tên lớp
            ->where(function($q) use ($query) {
                $q->where('fullname', 'LIKE', "%{$query}%")
                  ->orWhere('student_code', 'LIKE', "%{$query}%");
            })
            ->take(5) // Giới hạn 5 kết quả
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->fullname,
                    'info' => $student->student_code, // MSSV
                    'sub_info' => $student->studentClass->code ?? '', // Lớp
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($student->fullname) . '&background=d1fae5&color=047857', // Màu xanh lá
                    'url' => route('admin.students.show', $student->id)
                ];
            });

        

        return response()->json([
            'students' => $students
        ]);
    }
}