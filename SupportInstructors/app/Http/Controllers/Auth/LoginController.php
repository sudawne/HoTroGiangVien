<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate(
            [
                'code'     => 'required|string',
                'password' => 'required|string|min:6',
            ],
            [
                'code.required'     => 'Vui lòng nhập MSSV hoặc MSGV',
                'password.required' => 'Vui lòng nhập mật khẩu',
                'password.min'      => 'Mật khẩu phải có ít nhất 6 ký tự',
            ]
        );

        $student = Student::where('student_code', $request->code)->first();

        $lecturer = null;
        if (!$student) {
            $lecturer = Lecturer::where('lecturer_code', $request->code)->first();
        }

        if (!$student && !$lecturer) {
            return back()->withErrors(['code' => 'MSSV / MSGV không tồn tại'])->withInput();
        }

        $userId = $student ? $student->user_id : $lecturer->user_id;
        $user = User::find($userId);

        if (!$user) {
            return back()->withErrors(['code' => 'Tài khoản hệ thống bị lỗi (User missing)'])->withInput();
        }
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['password' => 'Mật khẩu không chính xác'])->withInput();
        }
        Auth::login($user);
        $request->session()->regenerate();

        return match ($user->role->name) {
            'ADMIN'    => redirect()->route('admin.dashboard'),
            'LECTURER' => redirect()->route('lecturer.dashboard'),
            'STUDENT'  => redirect()->route('student.dashboard'),
            default    => abort(403, 'Tài khoản chưa được phân quyền'),
        };
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
