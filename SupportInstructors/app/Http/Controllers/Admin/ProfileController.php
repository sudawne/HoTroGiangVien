<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Hash, Mail, DB, Auth};
use App\Models\User;
use Carbon\Carbon;

class ProfileController extends Controller
{
    public function showChangePassword() {
        return view('admin.profile.change-password');
    }

    public function sendOtp(Request $request) {
        $user = Auth::user();
        if (!$user) return response()->json(['message' => 'Lỗi xác thực'], 401);

        $otp = rand(100000, 999999);

        DB::table('password_reset_otps')->updateOrInsert(
            ['email' => $user->email],
            [
                'otp' => Hash::make($otp),
                'expires_at' => Carbon::now()->addMinutes(10),
                'created_at' => now()
            ]
        );

        Mail::raw("Mã xác nhận (OTP) để đổi mật khẩu của bạn là: $otp. Mã có hiệu lực trong 10 phút.", function ($message) use ($user) {
            $message->to($user->email)->subject('Xác nhận đổi mật khẩu - Cố vấn học tập');
        });

        return response()->json(['success' => true, 'message' => 'Mã OTP đã được gửi!']);
    }

    public function updatePassword(Request $request) {
        $request->validate([
            'otp' => 'required|numeric',
            'password' => [
                'required',
                'confirmed',
                'min:6',            
                'regex:/[a-z]/',    
                'regex:/[A-Z]/',      
                'regex:/[!@#$%^&*(),.?\":{}|<>]/',
            ],
        ], [
            'password.regex' => 'Mật khẩu phải bao gồm chữ hoa, chữ thường và ký tự đặc biệt.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.'
        ]);

        $user = Auth::user();
        $otpData = DB::table('password_reset_otps')->where('email', $user->email)->first();

        if (!$otpData || !Hash::check($request->otp, $otpData->otp) || Carbon::parse($otpData->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'Mã OTP không chính xác hoặc đã hết hạn.']);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->password)
        ]);
        DB::table('password_reset_otps')->where('email', $user->email)->delete();
        return redirect()->back()->with('success', 'Đổi mật khẩu thành công!');
    }
}