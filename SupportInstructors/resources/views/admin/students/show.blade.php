@extends('layouts.admin')
@section('title', 'Hồ sơ Sinh viên: ' . $student->student_code)

@section('content')
    <div class="w-full px-4 py-6" x-data="{ activeTab: 'warnings', selectedSemester: 'all' }">

        {{-- HEADER & BACK BUTTON --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.students.index') }}"
                    class="p-2 bg-white dark:bg-[#1e1e2d] border border-slate-300 dark:border-slate-700 rounded-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 shadow-sm transition-all">
                    <span class="material-symbols-outlined !text-[18px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        Hồ sơ Sinh viên
                        @if ($student->trashed())
                            <span
                                class="bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-sm border border-red-200 font-medium">Đã
                                ẩn</span>
                        @endif
                    </h1>
                    <p class="text-sm text-slate-500">Xem tổng quan học tập và lịch sử tư vấn</p>
                </div>
            </div>

            <div class="flex gap-2">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-medium rounded-md shadow-sm hover:bg-primary/90 transition-all active:scale-95">
                    <span class="material-symbols-outlined !text-[18px]">chat</span>
                    Nhắn tin nhanh
                </button>
            </div>
        </div>

        {{-- 1. HERO PROFILE BANNER --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm p-6 mb-6">
            <div class="flex flex-col md:flex-row items-center md:items-start gap-6">

                {{-- Avatar --}}
                <div class="relative shrink-0">
                    <div
                        class="w-28 h-28 rounded-lg border-2 border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden bg-slate-100 dark:bg-slate-800">
                        @if ($student->user && $student->user->avatar_url)
                            <img src="{{ asset('storage/' . $student->user->avatar_url) }}" alt="Avatar"
                                class="w-full h-full object-cover">
                        @else
                            <div
                                class="w-full h-full flex items-center justify-center text-slate-400 dark:text-slate-500 text-4xl font-bold uppercase">
                                {{ substr($student->fullname, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Thông tin chính --}}
                <div class="flex-1 text-center md:text-left">
                    @php
                        $statusData = match ($student->status) {
                            'studying' => [
                                'color' => 'bg-green-100 text-green-700 border-green-200',
                                'text' => 'Đang học',
                                'icon' => 'school',
                            ],
                            'reserved' => [
                                'color' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                'text' => 'Bảo lưu',
                                'icon' => 'pause_circle',
                            ],
                            'dropped' => [
                                'color' => 'bg-red-100 text-red-700 border-red-200',
                                'text' => 'Thôi học',
                                'icon' => 'cancel',
                            ],
                            'graduated' => [
                                'color' => 'bg-blue-100 text-blue-700 border-blue-200',
                                'text' => 'Tốt nghiệp',
                                'icon' => 'workspace_premium',
                            ],
                            default => [
                                'color' => 'bg-gray-100 text-gray-700 border-gray-200',
                                'text' => 'Không xác định',
                                'icon' => 'help',
                            ],
                        };
                    @endphp

                    <div class="flex flex-col md:flex-row md:items-center gap-3 mb-2 justify-center md:justify-start">
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $student->fullname }}</h2>
                        <span
                            class="px-2 py-1 text-[11px] font-bold rounded-sm border {{ $statusData['color'] }} flex items-center justify-center gap-1 w-fit mx-auto md:mx-0">
                            <span class="material-symbols-outlined !text-[14px]">{{ $statusData['icon'] }}</span>
                            {{ $statusData['text'] }}
                        </span>
                    </div>

                    <p class="text-base font-mono text-primary mb-4 font-semibold">{{ $student->student_code }}</p>

                    <div
                        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm text-slate-600 dark:text-slate-400 border-t border-slate-100 dark:border-slate-700 pt-4">
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <div
                                class="w-8 h-8 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[16px] text-slate-500">meeting_room</span>
                            </div>
                            <div>
                                <span class="block text-[11px] text-slate-400 uppercase">Lớp sinh hoạt</span>
                                <strong
                                    class="text-slate-700 dark:text-slate-200">{{ $student->class->code ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <div
                                class="w-8 h-8 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[16px] text-slate-500">calendar_month</span>
                            </div>
                            <div>
                                <span class="block text-[11px] text-slate-400 uppercase">Khóa học</span>
                                <strong
                                    class="text-slate-700 dark:text-slate-200">{{ $student->enrollment_year ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <div
                                class="w-8 h-8 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[16px] text-slate-500">mail</span>
                            </div>
                            <div>
                                <span class="block text-[11px] text-slate-400 uppercase">Email</span>
                                <strong class="text-slate-700 dark:text-slate-200 truncate w-32 block"
                                    title="{{ $student->user->email ?? '' }}">{{ $student->user->email ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        <div class="flex items-center justify-center md:justify-start gap-2">
                            <div
                                class="w-8 h-8 rounded-md bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[16px] text-slate-500">call</span>
                            </div>
                            <div>
                                <span class="block text-[11px] text-slate-400 uppercase">Điện thoại</span>
                                <strong
                                    class="text-slate-700 dark:text-slate-200">{{ $student->user->phone ?? 'Chưa có' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. DÃY CHỈ SỐ HỌC TẬP (STATS ROW) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Stat 1 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1 uppercase">GPA Tích lũy</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">
                        {{ $latestResult->accumulated_gpa_4 ?? '0.0' }} <span class="text-sm font-medium text-slate-400">/
                            4.0</span>
                    </h3>
                </div>
                <div
                    class="w-12 h-12 rounded-md bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center border border-blue-100 dark:border-blue-800">
                    <span class="material-symbols-outlined !text-[24px]">monitoring</span>
                </div>
            </div>

            {{-- Stat 2 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1 uppercase">Tín chỉ tích lũy</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">
                        {{ $latestResult->accumulated_credits ?? '0' }} <span
                            class="text-sm font-medium text-slate-400">TC</span>
                    </h3>
                </div>
                <div
                    class="w-12 h-12 rounded-md bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 flex items-center justify-center border border-emerald-100 dark:border-emerald-800">
                    <span class="material-symbols-outlined !text-[24px]">library_books</span>
                </div>
            </div>

            {{-- Stat 3 (Nguy hiểm) --}}
            @php $debtCount = $student->debts->count(); @endphp
            <div
                class="bg-white dark:bg-[#1e1e2d] border {{ $debtCount > 0 ? 'border-red-200 dark:border-red-800/50 bg-red-50/30' : 'border-slate-200 dark:border-slate-700' }} p-4 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p
                        class="text-xs font-semibold uppercase {{ $debtCount > 0 ? 'text-red-500' : 'text-slate-500 dark:text-slate-400' }} mb-1">
                        Đang nợ
                    </p>
                    <h3
                        class="text-2xl font-bold {{ $debtCount > 0 ? 'text-red-600' : 'text-slate-800 dark:text-white' }}">
                        {{ $debtCount }} <span
                            class="text-sm font-medium {{ $debtCount > 0 ? 'text-red-400' : 'text-slate-400' }}">TC</span>
                    </h3>
                </div>
                <div
                    class="w-12 h-12 rounded-md {{ $debtCount > 0 ? 'bg-red-100 text-red-600 border border-red-200' : 'bg-slate-100 text-slate-400 border border-slate-200' }} flex items-center justify-center">
                    <span class="material-symbols-outlined !text-[24px]">warning</span>
                </div>
            </div>

            {{-- Stat 4 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1 uppercase">Điểm rèn luyện</p>
                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white">
                        {{ $latestResult->training_point ?? '0' }} <span
                            class="text-sm font-medium text-slate-400">điểm</span>
                    </h3>
                </div>
                <div
                    class="w-12 h-12 rounded-md bg-purple-50 dark:bg-purple-900/20 text-purple-600 flex items-center justify-center border border-purple-100 dark:border-purple-800">
                    <span class="material-symbols-outlined !text-[24px]">military_tech</span>
                </div>
            </div>
        </div>

        {{-- 3. BỐ CỤC CHÍNH (CỘT NHỎ TRÁI - TABS LỚN PHẢI) --}}
        <div class="flex flex-col xl:flex-row gap-6">

            {{-- Cột trái: Liên hệ Gia đình --}}
            <div class="w-full xl:w-1/3 flex flex-col gap-6">
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm p-5">
                    <h3
                        class="font-bold text-slate-800 dark:text-white text-base mb-4 flex items-center gap-2 border-b border-slate-100 dark:border-slate-700 pb-3">
                        <span class="material-symbols-outlined text-orange-500 !text-[20px]">family_home</span> Gia đình &
                        Cá nhân
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div
                                class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-md border border-slate-100 dark:border-slate-700/50">
                                <p class="text-xs text-slate-500 mb-0.5">Ngày sinh</p>
                                <p class="font-medium text-slate-800 dark:text-slate-200 text-sm">
                                    {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '--' }}
                                </p>
                            </div>
                            <div
                                class="bg-slate-50 dark:bg-slate-800/50 p-2.5 rounded-md border border-slate-100 dark:border-slate-700/50">
                                <p class="text-xs text-slate-500 mb-0.5">Nơi sinh</p>
                                <p class="font-medium text-slate-800 dark:text-slate-200 text-sm">
                                    {{ $student->pob ?? '--' }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-2">
                            <p class="text-xs font-bold uppercase text-slate-500 mb-3">Người thân (Khẩn cấp)</p>
                            @forelse($student->relatives ?? [] as $relative)
                                <div
                                    class="bg-white dark:bg-[#1e1e2d] p-3.5 rounded-md border border-slate-200 dark:border-slate-700 mb-3 relative">
                                    <span
                                        class="absolute top-3.5 right-3.5 text-[10px] font-bold bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 px-2 py-0.5 rounded-sm text-slate-600">
                                        {{ $relative->relationship }}
                                    </span>
                                    <h4 class="font-bold text-slate-800 dark:text-slate-200 mb-2 text-sm">
                                        {{ $relative->fullname }}</h4>
                                    <div class="flex items-center gap-2 text-sm text-slate-600 dark:text-slate-400">
                                        <span
                                            class="material-symbols-outlined !text-[16px] text-slate-400">phone_in_talk</span>
                                        <a href="tel:{{ $relative->phone }}"
                                            class="font-medium hover:text-primary transition-colors">{{ $relative->phone }}</a>
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="text-sm text-slate-500 italic p-3 bg-slate-50 rounded-md border border-dashed border-slate-300 text-center">
                                    Chưa có thông tin liên hệ phụ huynh.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Khu vực Tabs --}}
            <div
                class="w-full xl:w-2/3 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm flex flex-col h-fit">

                {{-- TABS NAVIGATION (Thiết kế vuông vắn hơn) --}}
                <div class="border-b border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30">
                    <div class="flex overflow-x-auto hide-scrollbar px-2 pt-2">
                        <button @click="activeTab = 'warnings'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'warnings' ? 'border-red-500 text-red-600 bg-white dark:bg-[#1e1e2d]' :
                                'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'">
                            <span class="material-symbols-outlined !text-[18px]">report</span>
                            Cảnh báo & Nợ môn
                            @if ($student->academic_warnings->count() > 0)
                                <span
                                    class="bg-red-100 text-red-600 border border-red-200 text-[10px] px-1.5 py-0.5 rounded-sm ml-1">{{ $student->academic_warnings->count() }}</span>
                            @endif
                        </button>

                        <button @click="activeTab = 'academic'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'academic' ? 'border-primary text-primary bg-white dark:bg-[#1e1e2d]' :
                                'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'">
                            <span class="material-symbols-outlined !text-[18px]">menu_book</span>
                            Điểm số
                        </button>

                        <button @click="activeTab = 'consultation'"
                            class="px-5 py-3 text-sm font-semibold border-b-2 transition-colors flex items-center gap-2 whitespace-nowrap"
                            :class="activeTab === 'consultation' ? 'border-primary text-primary bg-white dark:bg-[#1e1e2d]' :
                                'border-transparent text-slate-500 hover:text-slate-700 hover:bg-slate-100/50'">
                            <span class="material-symbols-outlined !text-[18px]">support_agent</span>
                            Lịch sử Cố vấn
                        </button>
                    </div>
                </div>

                {{-- TABS CONTENT --}}
                <div class="p-6">

                    {{-- TAB 1: CẢNH BÁO & NỢ MÔN --}}
                    <div x-show="activeTab === 'warnings'" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">

                        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Lịch sử Cảnh báo học vụ</h3>
                        <div class="space-y-4 mb-8">
                            @forelse($student->academic_warnings ?? [] as $warning)
                                @php
                                    $warningColor = match ((int) $warning->warning_level) {
                                        1 => 'bg-yellow-50 border-l-4 border-yellow-400 text-yellow-800',
                                        2 => 'bg-orange-50 border-l-4 border-orange-500 text-orange-800',
                                        3 => 'bg-red-50 border-l-4 border-red-600 text-red-800',
                                        default => 'bg-gray-50 border-l-4 border-gray-400 text-gray-800',
                                    };
                                    $iconColor = match ((int) $warning->warning_level) {
                                        1 => 'text-yellow-500',
                                        2 => 'text-orange-500',
                                        3 => 'text-red-600',
                                        default => 'text-gray-500',
                                    };
                                @endphp
                                <div
                                    class="p-4 rounded-r-md border border-y-slate-200 border-r-slate-200 shadow-sm {{ $warningColor }} flex gap-4">
                                    <div class="mt-0.5"><span
                                            class="material-symbols-outlined !text-[24px] {{ $iconColor }}">warning</span>
                                    </div>
                                    <div class="w-full">
                                        <div class="flex justify-between items-start mb-2">
                                            <h4 class="font-bold text-base">
                                                Cảnh báo mức {{ $warning->warning_level }}
                                                <span
                                                    class="font-normal opacity-70 text-sm ml-1">({{ $warning->semester->name ?? 'N/A' }})</span>
                                            </h4>
                                            <span
                                                class="text-[10px] uppercase font-bold border px-2 py-0.5 rounded-sm bg-white shadow-sm {{ $warning->status == 'pending' ? 'text-red-600 border-red-200' : 'text-green-600 border-green-200' }}">
                                                {{ $warning->status }}
                                            </span>
                                        </div>
                                        <p class="text-sm font-medium mb-3">Lý do: {{ $warning->reason }}</p>

                                        <div
                                            class="flex flex-wrap gap-2 text-xs bg-white/60 p-2 rounded-md border border-black/5 mb-3">
                                            <div class="bg-white px-2 py-1 rounded-sm border border-slate-100">GPA Kỳ:
                                                <strong class="text-slate-800">{{ $warning->gpa_term }}</strong>
                                            </div>
                                            <div class="bg-white px-2 py-1 rounded-sm border border-slate-100">Nợ: <strong
                                                    class="text-red-600">{{ $warning->credits_owed }} TC</strong></div>
                                            <div class="bg-white px-2 py-1 rounded-sm border border-slate-100">Lần cảnh
                                                báo: <strong class="text-slate-800">Thứ
                                                    {{ $warning->warning_count }}</strong></div>
                                        </div>

                                        @if ($warning->advisor_note)
                                            <div class="bg-white p-3 rounded-md text-sm border border-slate-200">
                                                <strong class="text-slate-600 flex items-center gap-1 mb-1">
                                                    <span class="material-symbols-outlined !text-[14px]">edit_note</span>
                                                    Ghi chú Cố vấn:
                                                </strong>
                                                {{ $warning->advisor_note }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="p-6 bg-green-50/50 border border-green-200 rounded-md flex flex-col items-center justify-center gap-2 text-green-700 text-sm text-center border-dashed">
                                    <span class="material-symbols-outlined !text-[32px] text-green-500">check_circle</span>
                                    <p class="font-medium">Tuyệt vời! Sinh viên hiện không có cảnh báo học vụ nào.</p>
                                </div>
                            @endforelse
                        </div>

                        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Chi tiết Môn nợ</h3>
                        <div class="overflow-x-auto border border-slate-200 dark:border-slate-700 rounded-md">
                            <table class="w-full text-left text-sm border-collapse">
                                <thead
                                    class="bg-slate-100 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-600 uppercase text-[11px] font-bold tracking-wider">
                                    <tr>
                                        <th class="px-4 py-3">Mã HP</th>
                                        <th class="px-4 py-3">Tên học phần</th>
                                        <th class="px-4 py-3 text-center">STC</th>
                                        <th class="px-4 py-3 text-center">Điểm</th>
                                        <th class="px-4 py-3">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="divide-y divide-slate-100 dark:divide-slate-700 bg-white dark:bg-transparent">
                                    @forelse($student->debts as $debt)
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                            <td class="px-4 py-3 font-mono text-slate-600">{{ $debt->course_code }}</td>
                                            <td class="px-4 py-3 font-medium text-slate-800 dark:text-slate-200">
                                                {{ $debt->course_name }}</td>
                                            <td class="px-4 py-3 text-center">
                                                <span
                                                    class="bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-sm text-slate-600 font-bold">{{ $debt->credits }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="text-red-600 font-bold">{{ $debt->score ?? '--' }}</span>
                                            </td>
                                            <td class="px-4 py-3 text-xs text-slate-500">{{ $debt->note ?? '--' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-5 py-8 text-center text-slate-500">
                                                <div class="flex flex-col items-center justify-center gap-2">
                                                    <span
                                                        class="material-symbols-outlined !text-[28px] text-slate-300">task_alt</span>
                                                    <p>Sinh viên không nợ học phần nào.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- TAB 2: KẾT QUẢ HỌC TẬP --}}
                    <div x-show="activeTab === 'academic'" style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">

                        <h3 class="font-bold text-lg text-slate-800 dark:text-white mb-4">Tổng hợp điểm theo kỳ</h3>

                        <div class="space-y-4">
                            @forelse($student->academic_results ?? [] as $result)
                                <div
                                    class="border border-slate-200 dark:border-slate-700 rounded-md overflow-hidden shadow-sm">
                                    <div
                                        class="bg-slate-50 dark:bg-slate-800/80 px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 rounded-md bg-white shadow-sm border border-slate-200 flex items-center justify-center text-primary font-bold">
                                                HK
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 dark:text-slate-200 text-base">
                                                    {{ $result->semester->name ?? 'Học kỳ N/A' }}
                                                </h4>
                                                <p class="text-xs text-slate-500 font-mono">
                                                    {{ $result->semester->academic_year ?? '' }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex gap-2">
                                            <div
                                                class="bg-white border border-slate-200 px-3 py-1.5 rounded-md text-sm text-center min-w-[70px]">
                                                <span class="block text-[10px] text-slate-400 uppercase font-bold">GPA
                                                    4</span>
                                                <strong class="text-primary">{{ $result->gpa_4 ?? '--' }}</strong>
                                            </div>
                                            <div
                                                class="bg-white border border-slate-200 px-3 py-1.5 rounded-md text-sm text-center min-w-[70px]">
                                                <span class="block text-[10px] text-slate-400 uppercase font-bold">GPA
                                                    10</span>
                                                <strong class="text-slate-700">{{ $result->gpa_10 ?? '--' }}</strong>
                                            </div>
                                        </div>
                                    </div>

                                    <div
                                        class="p-4 text-sm text-slate-600 dark:text-slate-400 bg-white dark:bg-[#1e1e2d] flex justify-between items-center">
                                        <div class="grid grid-cols-2 md:grid-cols-3 gap-x-8 gap-y-2 w-full">
                                            <div>
                                                <span class="text-xs text-slate-400 block mb-0.5">Điểm rèn luyện</span>
                                                <strong
                                                    class="text-slate-800 dark:text-slate-200">{{ $result->training_point ?? '--' }}</strong>
                                            </div>
                                            <div>
                                                <span class="text-xs text-slate-400 block mb-0.5">Xếp loại</span>
                                                <strong
                                                    class="uppercase text-slate-800 dark:text-slate-200">{{ $result->classification ?? 'Chưa xét' }}</strong>
                                            </div>
                                            <div>
                                                <span class="text-xs text-slate-400 block mb-0.5">Tín chỉ tích lũy</span>
                                                <strong
                                                    class="text-slate-800 dark:text-slate-200">{{ $result->accumulated_credits ?? 0 }}
                                                    TC</strong>
                                            </div>
                                        </div>

                                        @if ($result->is_warning)
                                            <div class="shrink-0 ml-4">
                                                <span
                                                    class="text-red-600 text-xs font-bold border border-red-200 bg-red-50 px-2 py-1 rounded-sm flex items-center gap-1">
                                                    <span class="material-symbols-outlined !text-[14px]">warning</span> Bị
                                                    cảnh báo
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 border border-dashed border-slate-300 rounded-md text-center bg-slate-50">
                                    <span
                                        class="material-symbols-outlined !text-[32px] text-slate-300 mb-2">folder_open</span>
                                    <p class="text-slate-500 text-sm">Chưa có dữ liệu điểm học tập.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- TAB 3: NHẬT KÝ TƯ VẤN --}}
                    <div x-show="activeTab === 'consultation'" style="display: none;"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0">

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="font-bold text-lg text-slate-800 dark:text-white">Lịch sử làm việc</h3>
                            <button
                                class="px-3 py-1.5 bg-primary text-white text-xs font-medium rounded-md hover:bg-primary/90 flex items-center gap-1.5 shadow-sm transition-transform active:scale-95">
                                <span class="material-symbols-outlined !text-[16px]">add</span> Thêm ghi chú
                            </button>
                        </div>

                        <div
                            class="relative ml-3 space-y-6 before:absolute before:inset-0 before:ml-[15px] before:-translate-x-px before:h-full before:w-0.5 before:bg-slate-200">
                            @forelse($student->consultation_logs ?? [] as $log)
                                <div class="relative flex items-start gap-4 z-10">
                                    {{-- Icon Timeline --}}
                                    <div
                                        class="flex items-center justify-center w-8 h-8 mt-1 rounded-md border border-slate-200 bg-white text-primary shrink-0 shadow-sm">
                                        <span class="material-symbols-outlined !text-[16px]">support_agent</span>
                                    </div>

                                    {{-- Card Content --}}
                                    <div class="flex-1 p-4 rounded-md bg-white border border-slate-200 shadow-sm">
                                        <div class="flex flex-col sm:flex-row sm:items-start justify-between mb-2 gap-2">
                                            <h4 class="font-bold text-slate-800 text-base leading-tight">
                                                {{ $log->topic }}</h4>
                                            <time
                                                class="text-xs font-mono text-slate-500 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-sm shrink-0">
                                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y') }}
                                            </time>
                                        </div>

                                        <div
                                            class="text-xs text-slate-500 mb-3 flex items-center gap-1.5 pb-2 border-b border-slate-100">
                                            <span class="material-symbols-outlined !text-[14px]">person</span>
                                            <span>Bởi <strong>{{ $log->advisor->user->name ?? 'N/A' }}</strong> - Kỳ:
                                                {{ $log->semester->name ?? 'N/A' }}</span>
                                        </div>

                                        <div class="text-sm text-slate-600 space-y-2">
                                            <p><strong>Nội dung:</strong> {{ $log->content }}</p>
                                            @if ($log->solution)
                                                <div
                                                    class="mt-2 p-3 bg-blue-50 border border-blue-100 rounded-md text-blue-800">
                                                    <strong class="flex items-center gap-1 mb-1">
                                                        <span
                                                            class="material-symbols-outlined !text-[14px]">lightbulb</span>
                                                        Giải pháp:
                                                    </strong>
                                                    {{ $log->solution }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-10 z-10 relative">
                                    <div
                                        class="w-12 h-12 bg-slate-100 border border-slate-200 rounded-md flex items-center justify-center mx-auto mb-3">
                                        <span class="material-symbols-outlined !text-[24px] text-slate-400">history</span>
                                    </div>
                                    <p class="text-slate-500 text-sm">Chưa có bản ghi tư vấn nào.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
