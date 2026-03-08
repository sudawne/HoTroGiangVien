@extends('layouts.admin')
@section('content')
<div class="max-w-md mx-auto mt-10 bg-white dark:bg-[#1e1e2d] p-6 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700" 
     x-data="{ otpSent: false, loading: false, timer: 0 }">
    
    <h2 class="text-xl font-bold text-slate-800 dark:text-white mb-6 flex items-center gap-2">
        <span class="material-symbols-outlined">lock_reset</span> Đổi mật khẩu
    </h2>

    @if(session('success'))
        <div class="bg-emerald-100 text-emerald-700 p-3 rounded mb-4 text-sm">{{ session('success') }}</div>
    @endif

    <form action="{{ route('admin.profile.update_password') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Email giảng viên</label>
            <div class="flex gap-2">
                <input type="text" value="{{ auth()->user()->email }}" disabled class="flex-1 bg-slate-100 dark:bg-slate-800 border-slate-300 rounded-lg text-sm">
                <button type="button" @click="
                    loading = true;
                    fetch('{{ route('admin.profile.send_otp') }}', {method:'POST', headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}})
                    .then(() => { otpSent = true; loading = false; timer = 60; let interval = setInterval(() => { timer--; if(timer <= 0) clearInterval(interval) }, 1000); });
                " :disabled="loading || timer > 0" class="px-4 py-2 bg-primary text-white rounded-lg text-sm font-bold disabled:opacity-50">
                    <span x-show="!loading && timer === 0">Gửi mã</span>
                    <span x-show="loading">...</span>
                    <span x-show="timer > 0" x-text="timer + 's'"></span>
                </button>
            </div>
        </div>

        <div x-show="otpSent" x-transition>
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mã xác nhận (OTP)</label>
                <input type="text" name="otp" required class="w-full mt-1 border-slate-300 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-primary">
                @error('otp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Mật khẩu mới</label>
                <input type="password" name="password" required class="w-full mt-1 border-slate-300 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-primary">
                <p class="text-[10px] text-slate-500 mt-1">Ít nhất 6 ký tự, 1 chữ hoa, 1 chữ thường, 1 ký tự đặc biệt.</p>
                @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" required class="w-full mt-1 border-slate-300 dark:bg-slate-800 dark:text-white rounded-lg focus:ring-primary">
            </div>

            <button type="submit" class="w-full py-3 bg-primary text-white rounded-lg font-bold shadow-lg shadow-primary/30">Xác nhận đổi mật khẩu</button>
        </div>
    </form>
</div>
@endsection