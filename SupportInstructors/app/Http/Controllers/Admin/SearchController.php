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
        if (strlen($query) < 2) {
            return response()->json(['students' => [], 'lecturers' => []]);
        }
        // Tìm theo: Họ tên hoặc MSSV
        $students = Student::with('studentClass')
            ->where(function($q) use ($query) {
                $q->where('fullname', 'LIKE', "%{$query}%")
                  ->orWhere('student_code', 'LIKE', "%{$query}%");
            })
            ->take(5) 
            ->get()
            ->map(function ($student) {
                return [
                    'id' => $student->id,
                    'name' => $student->fullname,
                    'info' => $student->student_code, 
                    'sub_info' => $student->studentClass->code ?? '', 
                    'avatar' => 'https://ui-avatars.com/api/?name=' . urlencode($student->fullname) . '&background=d1fae5&color=047857', 
                    'url' => route('admin.students.show', $student->id)
                ];
            });
        return response()->json([
            'students' => $students
        ]);
    }
}