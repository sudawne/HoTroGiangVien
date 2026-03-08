@php
    $user = Auth::user();
    $student = $user->student ?? null;
    $class = $student ? $student->class : null;
    $advisor = $class ? $class->advisor : null;
@endphp

<aside
    class="fixed inset-y-0 left-0 z-30 w-64 lg:w-72 bg-white dark:bg-[#1e1e2d] border-r border-slate-200 dark:border-slate-700 flex flex-col transition-transform duration-300 transform md:static md:translate-x-0 h-screen"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" x-cloak>

    {{-- LOGO & TIÊU ĐỀ (Hiện trên cả Desktop và Mobile) --}}
    <div
        class="h-[60px] flex items-center justify-between px-5 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
        <a href="{{ url('/student') }}"
            class="flex items-center gap-3 text-slate-800 dark:text-white hover:opacity-80 transition-opacity w-full">
            <div class="bg-primary/10 p-1.5 rounded text-primary flex items-center justify-center">
                <span class="material-symbols-outlined !text-[20px]">school</span>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="font-bold text-[14px] leading-tight truncate">Cổng Sinh Viên</h1>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium truncate">Đại học Kiên Giang</p>
            </div>
        </a>

        {{-- Nút đóng sidebar (Chỉ hiện trên Mobile) --}}
        <button @click="sidebarOpen = false"
            class="md:hidden text-slate-500 hover:text-red-500 transition-colors p-1 bg-slate-50 rounded ml-2">
            <span class="material-symbols-outlined !text-[20px]">close</span>
        </button>
    </div>

    {{-- KHU VỰC NỘI DUNG SIDEBAR --}}
    <div class="flex-1 overflow-y-auto custom-scrollbar flex flex-col pt-5 px-4 pb-4">

        {{-- THÔNG TIN SINH VIÊN --}}
        <div
            class="bg-slate-50/60 border border-slate-200 dark:border-slate-700 rounded-xl p-4 mb-6 flex gap-3.5 items-center shadow-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-blue-50/50 to-transparent opacity-50"></div>

            <div class="relative z-10 flex-none">
                @if ($user && $user->avatar_url)
                    <img src="{{ asset('storage/' . $user->avatar_url) }}"
                        class="w-12 h-12 rounded-lg object-cover border border-slate-200 shadow-sm" alt="avatar">
                @else
                    <div
                        class="w-12 h-12 rounded-lg bg-blue-600 text-white flex items-center justify-center font-bold text-[18px] shadow-sm">
                        {{ mb_substr($user->name ?? 'S', 0, 1) }}
                    </div>
                @endif
            </div>

            <div class="relative z-10 flex-1 min-w-0">
                <h3 class="text-[14px] font-extrabold text-slate-800 dark:text-slate-100 leading-tight truncate"
                    title="{{ $user->name ?? '' }}">
                    {{ $user->name ?? '' }}
                </h3>
                <p class="text-slate-500 dark:text-slate-400 text-[11.5px] font-medium mt-1 truncate">
                    MSSV: <span
                        class="font-bold text-slate-700 dark:text-slate-300">{{ $student->student_code ?? 'Chưa cập nhật' }}</span>
                </p>
                <p class="text-[11.5px] font-semibold mt-0.5 text-blue-600 dark:text-blue-400 truncate">
                    Lớp: {{ $class->code ?? 'Chưa xếp lớp' }}
                </p>
            </div>
        </div>

        {{-- MENU HỆ THỐNG --}}
        <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-2 ml-1">Menu Hệ Thống</div>
        <nav class="flex flex-col gap-1.5 mb-6 font-display">
            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 {{ request()->routeIs('student.index') || request()->is('/') || request()->is('student') ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-slate-600 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-slate-300 dark:hover:text-white' }}"
                href="{{ url('/student') }}">
                <span class="material-symbols-outlined !text-[18px]"
                    {{ request()->routeIs('student.index') || request()->is('/') || request()->is('student') ? 'style=font-variation-settings:"FILL"1' : '' }}>feed</span>
                <span class="text-[13px]">Bảng tin</span>
            </a>

            <a class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 text-slate-600 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-slate-300 dark:hover:text-white"
                href="#">
                <span class="material-symbols-outlined !text-[18px]">school</span>
                <span class="text-[13px]">Kết quả học tập</span>
            </a>

            <a class="flex items-center justify-between px-3 py-2.5 rounded-lg transition-all duration-200 text-slate-600 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 dark:text-slate-300 dark:hover:text-white"
                href="#">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined !text-[18px]">warning</span>
                    <span class="text-[13px]">Cảnh báo học vụ</span>
                </div>
                <span class="bg-red-100 text-red-600 text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">0</span>
            </a>
        </nav>

        {{-- CỐ VẤN HỌC TẬP (Luôn đẩy xuống dưới cùng màn hình nếu còn chỗ trống) --}}
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-700/50">
            <div class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider mb-3 ml-1">Cố vấn học tập
            </div>

            <div
                class="bg-slate-50/80 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-xl p-3.5">
                @if ($advisor && $advisor->user)
                    <div class="flex items-center gap-3 mb-3.5">
                        <div class="flex-none">
                            @if ($advisor->user->avatar_url)
                                <img src="{{ asset('storage/' . $advisor->user->avatar_url) }}"
                                    class="w-10 h-10 rounded-full object-cover border-2 border-emerald-200 shadow-sm"
                                    alt="advisor">
                            @else
                                <div
                                    class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[14px] border-2 border-emerald-200 shadow-sm">
                                    {{ mb_substr($advisor->user->name, 0, 1) }}
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-bold text-[13px] text-slate-800 dark:text-slate-100 leading-tight truncate"
                                title="{{ $advisor->degree ? $advisor->degree . '. ' : '' }}{{ $advisor->user->name }}">
                                {{ $advisor->degree ? $advisor->degree . '. ' : '' }}{{ $advisor->user->name }}
                            </p>
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">
                                {{ $advisor->position ?? 'Giảng viên' }}</p>
                        </div>
                    </div>

                    <div class="space-y-2.5 mb-4 px-1">
                        <a href="mailto:{{ $advisor->user->email }}"
                            class="flex items-center gap-2.5 text-[12px] text-slate-600 dark:text-slate-300 hover:text-primary transition-colors"
                            title="{{ $advisor->user->email }}">
                            <span class="material-symbols-outlined !text-[15px] text-slate-400">mail</span>
                            <span class="truncate">{{ $advisor->user->email }}</span>
                        </a>
                        @if ($advisor->user->phone)
                            <div class="flex items-center gap-2.5 text-[12px] text-slate-600 dark:text-slate-300">
                                <span class="material-symbols-outlined !text-[15px] text-slate-400">call</span>
                                <span>{{ $advisor->user->phone }}</span>
                            </div>
                        @endif
                    </div>

                    <button
                        class="w-full bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-600 hover:text-white hover:border-emerald-600 py-2 rounded-lg text-[12.5px] font-bold transition-all flex justify-center items-center gap-1.5 shadow-sm">
                        <span class="material-symbols-outlined !text-[16px]">calendar_month</span> Đặt lịch hẹn
                    </button>
                @else
                    <div class="text-center py-4 flex flex-col items-center opacity-60">
                        <span class="material-symbols-outlined !text-[32px] mb-2 text-slate-400">person_off</span>
                        <p class="text-[12px] text-slate-500 font-medium">Lớp hiện chưa có Cố vấn.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>
</aside>
