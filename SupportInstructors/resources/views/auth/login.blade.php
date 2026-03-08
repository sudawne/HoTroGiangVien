@extends('layouts.loginLayout')

@section('title', 'Đăng nhập hệ thống')

@section('content')
    <div class="w-full md:w-[360px] relative z-0 px-4 md:px-0">
        <div class="bg-primary h-9 rounded-t-lg relative flex justify-start items-center px-4">
            <div
                class="absolute -bottom-4 left-4 w-9 h-9 bg-surface-light dark:bg-surface-dark dark:border-surface-dark rounded-md shadow-md flex items-center justify-center z-10">
                <span class="material-icons text-yellow-600 text-3xl">lock_person</span>
            </div>
        </div>

        <div
            class="bg-surface-light dark:bg-surface-dark rounded-b-lg shadow-login p-4 pt-3 border-t-4 border-gray-200 dark:border-gray-700">
            @if (session('status'))
                <div class="mb-3 text-sm text-green-600">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="mb-3 text-sm text-red-600">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('auth.login') }}" class="space-y-3 mt-3">
                @csrf

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <span
                            class="material-icons text-blue-500 group-focus-within:text-primary transition-colors text-base text-lg">account_circle</span>
                    </div>
                    <input type="text" name="code" value="{{ old('code') }}" required placeholder="Mã số đăng nhập"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out shadow-sm" />
                    @error('code')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                        <span
                            class="material-icons text-yellow-500 group-focus-within:text-yellow-600 transition-colors text-base">vpn_key_alert</span>
                    </div>
                    <input type="password" name="password" required placeholder="Mật khẩu"
                        class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out shadow-sm" />
                    @error('password')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between pt-3">
                    <a class="text-sm font-medium text-primary hover:text-primary-hover dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                        href="{{ route('auth.forgot_password') }}">
                        Quên mật khẩu
                    </a>

                    <button
                        class="flex justify-center py-2 px-4 border border-transparent text-sm font-bold rounded-md text-white bg-primary hover:bg-primary-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow transition-all duration-150 uppercase tracking-wide"
                        type="submit">
                        Đăng nhập
                    </button>
                </div>
            </form>
        </div>

        <div class="absolute bottom-0 left-4 right-4 h-3 bg-black/15 blur-md rounded-[50%] -z-10"></div>
    </div>

    <div
        class="w-full md:flex-1 bg-surface-light dark:bg-surface-dark rounded-lg shadow-card border border-gray-200 dark:border-gray-700 min-h-[260px] relative mt-6 md:mt-0 px-4 md:px-6 flex flex-col">
        <div
            class="absolute -top-3 left-1/2 transform -translate-x-1/2 bg-gray-50 dark:bg-gray-800 px-6 py-1.5 rounded-lg border border-gray-200 dark:border-gray-600 shadow-sm z-10">
            <h3 class="text-accent-red font-bold uppercase text-xs tracking-wide">Thông báo mới nhất</h3>
        </div>

        <div class="mt-8 mb-4 flex-1 overflow-y-auto pr-1 custom-scrollbar">
            @if (isset($notifications) && $notifications->count() > 0)
                <div class="space-y-3">
                    @foreach ($notifications as $notification)
                        @php
                            // Đặt icon và màu sắc tương ứng với type của thông báo
                            $icon = 'info';
                            $color = 'text-blue-500';

                            if ($notification->type == 'warning') {
                                $icon = 'warning';
                                $color = 'text-yellow-500';
                            } elseif ($notification->type == 'urgent') {
                                $icon = 'error_outline';
                                $color = 'text-red-500';
                            } elseif ($notification->type == 'batch_alert') {
                                $icon = 'notification_important';
                                $color = 'text-purple-500';
                            }
                        @endphp

                        <div onclick="showToast('warning', 'Vui lòng đăng nhập hệ thống để xem chi tiết thông báo này!');"
                            class="block p-3 border border-gray-100 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors cursor-pointer relative group">

                            <div class="flex items-start gap-3">
                                <span class="material-icons mt-0.5 {{ $color }} text-xl">{{ $icon }}</span>
                                <div class="flex-1">
                                    <h4
                                        class="text-sm font-semibold text-gray-800 dark:text-gray-200 line-clamp-2 group-hover:text-primary transition-colors">
                                        {{ $notification->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                        <span class="material-icons text-[12px]">schedule</span>
                                        {{ \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 py-10">
                    <span class="material-icons text-5xl opacity-20 mb-2">notifications_off</span>
                    <p class="text-sm">Hiện chưa có thông báo mới.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
