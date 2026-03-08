@extends('layouts.loginLayout')

@section('title', 'Khôi phục mật khẩu')

@section('content')
{{-- 1. CSS ẨN CÁC PHẦN TỬ KHI LOAD TRANG (Chống giật) --}}
<style>
    [x-cloak] { display: none !important; }
</style>

{{-- 2. CONTAINER CHÍNH --}}
<div class="flex items-center justify-center min-h-screen w-full px-4 bg-gray-50 dark:bg-gray-900" 
     x-data="forgotPasswordApp()">

    {{-- TOAST NOTIFICATION --}}
    <div class="fixed top-5 right-5 z-50 w-full max-w-xs space-y-2 pointer-events-none" x-cloak>
        <template x-for="note in notifications" :key="note.id">
            <div x-show="note.show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-x-8"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 class="pointer-events-auto bg-white dark:bg-gray-800 shadow-lg rounded-xl p-4 border-l-4 flex items-center gap-3"
                 :class="note.type === 'success' ? 'border-emerald-500' : 'border-red-500'">
                <span class="material-icons" :class="note.type === 'success' ? 'text-emerald-500' : 'text-red-500'" x-text="note.type === 'success' ? 'check_circle' : 'error'"></span>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-200" x-text="note.message"></p>
            </div>
        </template>
    </div>

    {{-- MAIN CARD --}}
    <div class="w-full max-w-[420px] bg-white dark:bg-[#1e1e2d] rounded-3xl shadow-2xl overflow-hidden relative border border-gray-100 dark:border-gray-800">
        
        {{-- Header Gradient --}}
        <div class="h-32 bg-gradient-to-br from-blue-600 to-indigo-600 relative flex items-center justify-center">
            {{-- Decoration circles --}}
            <div class="absolute w-40 h-40 bg-white/10 rounded-full -top-10 -right-10 blur-2xl"></div>
            <div class="absolute w-32 h-32 bg-white/10 rounded-full bottom-0 left-0 blur-xl"></div>
            
            <div class="text-center text-white z-10 mt-4">
                <div class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center mx-auto mb-3 shadow-inner border border-white/20">
                    <span class="material-icons text-3xl">lock_reset</span>
                </div>
                <h2 class="text-xl font-bold">Khôi phục mật khẩu</h2>
            </div>
        </div>

        <div class="p-8">

            {{-- ========================================= --}}
            {{-- FORM 1: NHẬP EMAIL --}}
            {{-- ========================================= --}}
            <div x-show="step === 1" 
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 -translate-x-10"
                 x-transition:enter-end="opacity-100 translate-x-0">
                
                <p class="text-center text-sm text-gray-500 dark:text-gray-400 mb-6">
                    Nhập email tài khoản của bạn. Hệ thống sẽ gửi mã OTP xác thực.
                </p>

                <div class="space-y-5">
                    <div class="relative group">
                        <input type="email" x-model="email" 
                            class="peer w-full pl-11 pr-4 py-3.5 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/50 transition-all placeholder-transparent shadow-inner"
                            placeholder="Email" id="email"
                            @keydown.enter="sendOtp()">
                        <label for="email" class="absolute left-11 top-3.5 text-gray-400 text-sm transition-all peer-placeholder-shown:text-sm peer-placeholder-shown:top-3.5 peer-focus:-top-2 peer-focus:text-xs peer-focus:text-blue-500 peer-not-placeholder-shown:-top-2 peer-not-placeholder-shown:text-xs peer-not-placeholder-shown:text-blue-500 pointer-events-none">Email của bạn</label>
                        <span class="material-icons absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 peer-focus:text-blue-500 transition-colors">mail</span>
                    </div>

                    <button @click="sendOtp()" :disabled="loading"
                        class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-500/30 transition-all active:scale-[0.98] disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 relative overflow-hidden group">
                        
                        {{-- Loading --}}
                        <div x-show="loading" style="display: none" class="absolute inset-0 bg-black/10 flex items-center justify-center backdrop-blur-[1px]">
                            <span class="material-icons animate-spin">sync</span>
                        </div>
                        
                        <span x-show="!loading" class="group-hover:mr-1 transition-all">Gửi mã xác nhận</span>
                        <span x-show="!loading" class="material-icons text-sm opacity-0 group-hover:opacity-100 transition-all -ml-2 group-hover:ml-0">arrow_forward</span>
                    </button>
                    
                    <div class="text-center mt-2">
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Quay lại đăng nhập</a>
                    </div>
                </div>
            </div>

            {{-- ========================================= --}}
            {{-- FORM 2: NHẬP OTP (Cần thêm x-cloak vào đây) --}}
            {{-- ========================================= --}}
            <div x-show="step === 2" x-cloak
                 x-transition:enter="transition ease-out duration-500 delay-100"
                 x-transition:enter-start="opacity-0 translate-x-10"
                 x-transition:enter-end="opacity-100 translate-x-0">
                
                <form method="POST" action="{{ route('auth.forgot_password.reset') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="email" x-model="email">

                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-2xl flex items-center gap-3 mb-4 border border-emerald-100 dark:border-emerald-800">
                        <div class="w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-800 text-emerald-600 dark:text-emerald-300 flex items-center justify-center shrink-0">
                            <span class="material-icons text-sm">mark_email_read</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs text-emerald-700 dark:text-emerald-400 font-bold">Đã gửi mã thành công!</p>
                            <p class="text-[10px] text-emerald-600/70 truncate" x-text="email"></p>
                        </div>
                    </div>

                    <div>
                        <input type="text" name="otp" required maxlength="6"
                            class="w-full py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-center text-2xl font-bold tracking-[0.5em] text-blue-600 focus:ring-2 focus:ring-blue-500/50 transition-all placeholder:text-gray-300 placeholder:tracking-normal placeholder:text-sm"
                            placeholder="••••••">
                    </div>

                    <div class="space-y-3">
                        <div class="relative">
                            <input type="password" name="password" required placeholder="Mật khẩu mới"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/50 transition-all text-sm">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">lock</span>
                        </div>
                        <div class="relative">
                            <input type="password" name="password_confirmation" required placeholder="Xác nhận mật khẩu"
                                class="w-full pl-10 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border-none rounded-2xl text-gray-800 dark:text-white focus:ring-2 focus:ring-blue-500/50 transition-all text-sm">
                            <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">verified_user</span>
                        </div>
                    </div>

                    <button type="submit" 
                        class="w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl font-bold shadow-lg shadow-emerald-500/30 transition-all active:scale-[0.98] mt-2">
                        Xác nhận đổi mật khẩu
                    </button>

                    <div class="flex items-center justify-between text-xs font-medium text-gray-500 pt-2 px-1">
                        <button type="button" @click="step = 1" class="hover:text-blue-600 transition-colors">Nhập lại Email</button>
                        <div class="flex items-center gap-1">
                            <span x-show="timer > 0" class="text-gray-400">Gửi lại: <span x-text="timer"></span>s</span>
                            <button type="button" x-show="timer === 0" @click="sendOtp()" class="text-blue-600 hover:underline font-bold flex items-center gap-1">
                                Gửi lại mã
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- 3. JAVASCRIPT LOGIC --}}
<script>
    function forgotPasswordApp() {
        return {
            step: {{ $errors->has('otp') || $errors->has('password') ? 2 : 1 }},
            email: '{{ old('email') }}',
            loading: false,
            timer: 0,
            notifications: [],

            sendOtp() {
                if (!this.email) { 
                    this.notify('Vui lòng nhập email trước!', 'error');
                    return; 
                }
                
                this.loading = true;

                fetch('{{ route('auth.forgot_password.send_otp') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ email: this.email })
                })
                .then(res => res.json())
                .then(data => {
                    this.loading = false;
                    if (data.success) {
                        this.step = 2; 
                        this.notify(data.message, 'success');
                        this.startTimer();
                    } else {
                        this.notify(data.message || 'Lỗi không xác định', 'error');
                    }
                })
                .catch(err => {
                    this.loading = false;
                    this.notify('Lỗi kết nối server!', 'error');
                });
            },

            startTimer() {
                this.timer = 60;
                let interval = setInterval(() => { 
                    this.timer--; 
                    if(this.timer <= 0) clearInterval(interval); 
                }, 1000);
            },

            notify(message, type = 'success') {
                const id = Date.now();
                this.notifications.push({ id, message, type, show: true });
                setTimeout(() => {
                    this.notifications = this.notifications.filter(n => n.id !== id);
                }, 4000);
            }
        }
    }
</script>

{{-- 4. NHÚNG ALPINE.JS (Phòng trường hợp Layout thiếu) --}}
@if(!Str::contains(file_get_contents(resource_path('views/layouts/loginLayout.blade.php')), 'alpine'))
    <script src="//unpkg.com/alpinejs" defer></script>
@endif

@endsection