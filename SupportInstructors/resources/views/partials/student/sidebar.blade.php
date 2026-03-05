@php
    $student = Auth::user()->student;
    $class = $student ? $student->class : null;
    $advisor = $class ? $class->advisor : null;
    $user = Auth::user();
@endphp

<aside
    class="col-span-12 md:col-span-4 lg:col-span-3 hidden md:block sticky top-[76px] max-h-[calc(100vh-80px)] overflow-y-auto hide-scroll">

    <div class="bg-white dark:bg-slate-800 rounded-sm shadow-sm border border-slate-200 flex flex-col">

        {{-- Nửa trên: Thông tin Sinh viên & Menu --}}
        <div class="p-4">
            <div class="flex gap-3 items-center mb-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="relative">
                        @if ($user && $user->avatar_url)
                            <img src="{{ asset('storage/' . $user->avatar_url) }}"
                                class="w-11 h-11 rounded-sm avatar-img border border-slate-200" alt="avatar">
                        @else
                            <div
                                class="w-11 h-11 rounded-sm bg-primary text-white flex items-center justify-center font-bold text-[16px] shadow-sm">
                                {{ substr($user->name ?? 'U', 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h1 class="text-[13px] font-bold text-slate-900 dark:text-slate-100 leading-tight truncate"
                            title="{{ $user->name ?? '' }}">
                            {{ $user->name ?? '' }}
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 text-[11px] font-normal mt-0.5">
                            MSSV: {{ $student->student_code ?? 'Chưa cập nhật' }}
                        </p>
                        <p class="text-[11px] text-blue-600 dark:text-blue-400 font-semibold mt-0.5 truncate">
                            Lớp: {{ $class->code ?? 'Chưa xếp lớp' }}
                        </p>
                    </div>
                </div>
            </div>

            <nav class="flex flex-col gap-1 font-display">
                <a class="flex items-center gap-2.5 px-3 py-2 rounded-sm font-medium transition-colors {{ request()->routeIs('student.index') ? 'bg-primary/10 text-primary dark:bg-blue-900/30 dark:text-blue-400' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}"
                    href="{{ url('/') }}">
                    <span class="material-symbols-outlined !text-[16px]">feed</span>
                    <span class="text-[12px]">Bảng tin</span>
                </a>
                <a class="flex items-center gap-2.5 px-3 py-2 rounded-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors font-medium"
                    href="#">
                    <span class="material-symbols-outlined !text-[16px]">school</span>
                    <span class="text-[12px]">Kết quả học tập</span>
                </a>
                <a class="flex items-center justify-between px-3 py-2 rounded-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors font-medium"
                    href="#">
                    <div class="flex items-center gap-2.5">
                        <span class="material-symbols-outlined !text-[16px]">warning</span>
                        <span class="text-[12px]">Cảnh báo học vụ</span>
                    </div>
                    <span class="bg-red-100 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded-sm">0</span>
                </a>
            </nav>
        </div>

        <div class="h-px w-full bg-slate-100 dark:bg-slate-700"></div>

        {{-- Nửa dưới: Cố vấn học tập --}}
        <div class="p-4 bg-slate-50/50 dark:bg-slate-800/50 rounded-b-sm">
            <h3
                class="font-bold text-[12px] text-slate-500 uppercase tracking-wider mb-3 flex items-center gap-2 font-display">
                Cố vấn học tập
            </h3>

            @if ($advisor && $advisor->user)
                <div class="flex items-center gap-2.5 mb-3.5">
                    <div class="relative">
                        @if ($advisor->user->avatar_url)
                            <img src="{{ asset('storage/' . $advisor->user->avatar_url) }}"
                                class="w-9 h-9 rounded-sm object-cover border border-emerald-200" alt="advisor">
                        @else
                            <div
                                class="w-9 h-9 rounded-sm bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-[13px] border border-emerald-200">
                                {{ substr($advisor->user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-[12.5px] text-slate-900 dark:text-slate-100 leading-tight truncate">
                            {{ $advisor->degree ? $advisor->degree . '. ' : '' }}{{ $advisor->user->name }}
                        </p>
                        <p class="text-[10px] text-slate-500 mt-0.5">{{ $advisor->position ?? 'Giảng viên' }}</p>
                    </div>
                </div>

                <div class="space-y-2 text-[11.5px] mb-3">
                    <a href="mailto:{{ $advisor->user->email }}"
                        class="flex items-center gap-2 text-slate-600 dark:text-slate-300 hover:text-primary transition-colors">
                        <span class="material-symbols-outlined !text-[14px] text-slate-400">mail</span>
                        <span class="truncate">{{ $advisor->user->email }}</span>
                    </a>
                    @if ($advisor->user->phone)
                        <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                            <span class="material-symbols-outlined !text-[14px] text-slate-400">call</span>
                            <span>{{ $advisor->user->phone }}</span>
                        </div>
                    @endif
                </div>

                <button
                    class="w-full bg-primary/10 text-primary hover:bg-primary hover:text-white py-1.5 rounded-sm text-[12px] font-bold transition-colors flex justify-center items-center gap-1.5">
                    <span class="material-symbols-outlined !text-[14px]">calendar_month</span>
                    Đặt lịch hẹn
                </button>
            @else
                <div class="text-center py-2">
                    <p class="text-[11px] text-slate-500">Lớp hiện chưa có Cố vấn học tập.</p>
                </div>
            @endif
        </div>

    </div>
</aside>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const CSRF_TOKEN = "{{ csrf_token() }}";

            // Helpers and AJAX handlers (unchanged)
            // ... (you can keep the existing AJAX code from previous file)
        });
    </script>
@endsection
