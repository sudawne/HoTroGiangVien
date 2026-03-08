@extends('layouts.loginLayout')

@section('title', 'Khôi phục mật khẩu')

@section('content')
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    {{-- Thêm flex, h-full, items-center để form luôn nằm giữa phần main và đẩy footer xuống đáy --}}
    <div class="w-full max-w-md mx-auto flex items-center justify-center min-h-full" x-data="forgotPasswordApp()">
        <div class="w-full relative z-0">

            {{-- TOAST NOTIFICATION --}}
            <div class="fixed top-5 right-5 z-50 w-full max-w-xs space-y-2 pointer-events-none" x-cloak>
                <template x-for="note in notifications" :key="note.id">
                    <div x-show="note.show" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-x-8" x-transition:enter-end="opacity-100 translate-x-0"
                        class="pointer-events-auto bg-surface-light dark:bg-surface-dark shadow-md border border-gray-200 dark:border-gray-700 rounded p-3 flex items-start gap-3 border-l-4"
                        :class="note.type === 'success' ? 'border-l-green-500' : 'border-l-red-500'">
                        <span class="material-icons text-xl"
                            :class="note.type === 'success' ? 'text-green-500' : 'text-red-500'"
                            x-text="note.type === 'success' ? 'check_circle' : 'error'"></span>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-200 mt-0.5" x-text="note.message"></p>
                    </div>
                </template>
            </div>

            {{-- MAIN CARD --}}
            <div
                class="w-full bg-surface-light dark:bg-surface-dark rounded-sm shadow-login border border-gray-200 dark:border-gray-700 overflow-hidden">

                {{-- Header --}}
                <div
                    class="p-6 border-b border-gray-100 dark:border-gray-700/50 text-center bg-gray-50/50 dark:bg-transparent">
                    <div
                        class="inline-flex items-center justify-center w-12 h-12 bg-primary/10 text-primary rounded-full mb-3">
                        <span class="material-icons text-2xl">lock_reset</span>
                    </div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-white uppercase tracking-wide">Khôi phục mật khẩu
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Hệ thống Quản lý Cố vấn Học tập</p>
                </div>

                <div class="p-6 sm:p-8">
                    {{-- ========================================= --}}
                    {{-- FORM 1: NHẬP EMAIL --}}
                    {{-- ========================================= --}}
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5 text-center px-2">
                            Vui lòng nhập email tài khoản của bạn. Hệ thống sẽ gửi mã OTP xác thực 6 chữ số.
                        </p>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Email liên
                                    kết
                                    <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span
                                            class="material-icons text-gray-400 group-focus-within:text-primary transition-colors text-lg">mail</span>
                                    </div>
                                    <input type="email" x-model="email"
                                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out shadow-sm"
                                        placeholder="VD: user@vnkgu.edu.vn" id="email" @keydown.enter="sendOtp()">
                                </div>
                            </div>

                            <button @click="sendOtp()" :disabled="loading"
                                class="w-full py-2 flex justify-center border border-transparent text-sm font-bold rounded-sm text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow transition-all duration-150 tracking-wide gap-2 mt-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="loading" class="material-icons animate-spin text-[18px]"
                                    style="display: none;">sync</span>
                                <span x-text="loading ? 'Đang xử lý...' : 'Nhận mã OTP'"></span>
                            </button>

                            <div class="text-center mt-6">
                                <a href="{{ route('login') }}"
                                    class="text-sm font-medium text-primary hover:text-primary-hover transition-colors inline-flex items-center gap-1">
                                    <span class="material-icons text-[16px]">arrow_back</span> Quay lại đăng nhập
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- ========================================= --}}
                    {{-- FORM 2: NHẬP OTP VÀ ĐỔI MẬT KHẨU --}}
                    {{-- ========================================= --}}
                    <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">

                        <form method="POST" action="{{ route('auth.forgot_password.reset') }}" class="space-y-4">
                            @csrf
                            <input type="hidden" name="email" x-model="email">

                            <div
                                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800/50 p-3 rounded-sm flex items-start gap-3 mb-2">
                                <span class="material-icons text-blue-600 mt-0.5 text-[20px]">mark_email_read</span>
                                <div class="flex-1">
                                    <p class="text-sm text-blue-800 dark:text-blue-300 font-bold mb-0.5">Đã gửi mã xác nhận!
                                    </p>
                                    <p class="text-xs text-blue-600 dark:text-blue-400">Vui lòng kiểm tra hộp thư <span
                                            class="font-bold" x-text="email"></span></p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mã OTP (6 số)
                                    <span class="text-red-500">*</span></label>
                                <input type="text" name="otp" required maxlength="6"
                                    class="w-full py-2 border border-gray-300 dark:border-gray-600 rounded-sm text-center text-xl font-bold tracking-[0.5em] text-gray-800 dark:text-white focus:ring-1 focus:ring-primary focus:border-primary dark:bg-gray-800 uppercase placeholder:tracking-normal placeholder:font-normal placeholder:text-sm shadow-sm"
                                    placeholder="Nhập mã OTP">
                                @error('otp')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="border-t border-gray-100 dark:border-gray-700 my-4"></div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Mật khẩu mới
                                    <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span
                                            class="material-icons text-gray-400 group-focus-within:text-primary transition-colors text-lg">lock</span>
                                    </div>
                                    <input type="password" name="password" required
                                        class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm text-sm focus:ring-1 focus:ring-primary focus:border-primary dark:bg-gray-800 dark:text-white transition-colors shadow-sm"
                                        placeholder="Nhập mật khẩu mới">
                                </div>
                                @error('password')
                                    <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-1.5">Xác nhận mật
                                    khẩu <span class="text-red-500">*</span></label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span
                                            class="material-icons text-gray-400 group-focus-within:text-primary transition-colors text-lg">verified_user</span>
                                    </div>
                                    <input type="password" name="password_confirmation" required
                                        class="w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-sm text-sm focus:ring-1 focus:ring-primary focus:border-primary dark:bg-gray-800 dark:text-white transition-colors shadow-sm"
                                        placeholder="Nhập lại mật khẩu mới">
                                </div>
                            </div>

                            <button type="submit"
                                class="w-full py-2 flex justify-center border border-transparent text-sm font-bold rounded-sm text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow transition-all duration-150 uppercase tracking-wide mt-4 gap-2">
                                <span class="material-icons text-[18px]">save</span> Xác nhận đổi
                            </button>

                            <div class="flex items-center justify-between text-xs font-medium text-gray-500 pt-5">
                                <button type="button" @click="step = 1"
                                    class="hover:text-primary transition-colors flex items-center gap-1 text-primary">
                                    <span class="material-icons text-[14px]">edit</span> Đổi Email
                                </button>

                                <div class="flex items-center gap-1">
                                    <span x-show="timer > 0" class="text-gray-400">Gửi lại sau: <span x-text="timer"
                                            class="font-bold text-gray-600 dark:text-gray-300"></span>s</span>
                                    <button type="button" x-show="timer === 0" @click="sendOtp()"
                                        class="text-primary hover:underline font-bold flex items-center gap-1">
                                        <span class="material-icons text-[14px]">refresh</span> Gửi lại mã
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                        this.notify('Vui lòng nhập địa chỉ email!', 'error');
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
                            body: JSON.stringify({
                                email: this.email
                            })
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
                            this.notify('Lỗi kết nối máy chủ!', 'error');
                        });
                },

                startTimer() {
                    this.timer = 60;
                    let interval = setInterval(() => {
                        this.timer--;
                        if (this.timer <= 0) clearInterval(interval);
                    }, 1000);
                },

                notify(message, type = 'success') {
                    const id = Date.now();
                    this.notifications.push({
                        id,
                        message,
                        type,
                        show: true
                    });
                    setTimeout(() => {
                        this.notifications = this.notifications.filter(n => n.id !== id);
                    }, 4000);
                }
            }
        }
    </script>
@endsection
