<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Mail, Hash, Validator};
use App\Models\User;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // Hiển thị View
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    // BƯỚC 1: Xử lý gửi OTP (Ajax)
    public function sendOtp(Request $request)
    {
        // 1. Validate Email (Kiểm tra tồn tại)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.email' => 'Email không hợp lệ.',
            'email.exists' => 'Email này chưa được đăng ký.'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 2. Gửi Mail
        try {
            $otp = rand(100000, 999999);

            // Lưu OTP vào DB
            DB::table('password_reset_otps')->updateOrInsert(
                ['email' => $request->email],
                [
                    'otp' => Hash::make($otp),
                    'expires_at' => Carbon::now()->addMinutes(10),
                    'created_at' => now()
                ]
            );

            // Gửi qua SMTP
            Mail::raw("Mã xác nhận (OTP) của bạn là: {$otp}", function ($message) use ($request) {
                $message->to($request->email)->subject('Mã xác nhận khôi phục mật khẩu');
            });

            return response()->json([
                'success' => true, 
                'message' => 'Đã gửi mã OTP thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Lỗi gửi mail: ' . $e->getMessage()
            ], 500);
        }
    }

    // BƯỚC 2: Đổi mật khẩu (Form Submit)
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required',
            'password' => 'required|confirmed|min:6|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/|regex:/[@$!%*#?&]/'
        ]);

        $otpData = DB::table('password_reset_otps')->where('email', $request->email)->first();

        if (!$otpData || !Hash::check($request->otp, $otpData->otp) || Carbon::parse($otpData->expires_at)->isPast()) {
            return back()->withErrors(['otp' => 'Mã OTP sai hoặc hết hạn.'])->withInput();
        }

        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        
        // Xóa OTP sau khi dùng
        DB::table('password_reset_otps')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công! Vui lòng đăng nhập.');
    }
}