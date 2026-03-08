@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col gap-6">

        {{-- CARDS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card 1: Tổng sinh viên (Link đến DS Sinh viên) --}}
            <a href="{{ route('admin.students.index') }}" class="group block">
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Tổng số sinh viên</p>
                        <span class="bg-primary/10 text-primary p-1 rounded">
                            <span class="material-symbols-outlined !text-[15px]">groups</span>
                        </span>
                    </div>
                    <div class="flex items-end gap-2">
                        <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($totalStudents) }}</h2>
                        @if($newStudentsCount > 0)
                            <span class="text-xs text-emerald-600 font-medium mb-1 flex items-center">
                                <span class="material-symbols-outlined !text-[12px]">trending_up</span> +{{ $newStudentsCount }}
                            </span>
                        @endif
                    </div>
                </div>
            </a>

            {{-- Card 2: Cảnh báo học tập (Link đến DS Cảnh báo) --}}
            <a href="{{ route('admin.academic_warnings.index') }}" class="group block">
                <div class="bg-white dark:bg-[#1e1e2d] border border-red-100 dark:border-red-900/30 rounded p-4 flex flex-col justify-between h-28 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-16 h-16 bg-red-50 dark:bg-red-900/10 rounded-bl-full -mr-4 -mt-4"></div>
                    <div class="flex justify-between items-start relative z-10">
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh báo học tập</p>
                        <span class="bg-red-100 text-red-600 p-1 rounded">
                            <span class="material-symbols-outlined !text-[15px]">warning</span>
                        </span>
                    </div>
                    <div class="flex items-end gap-2 relative z-10">
                        <h2 class="text-3xl font-bold text-red-600">{{ number_format($warningCount) }}</h2>
                        <span class="text-xs text-slate-400 mb-1">sinh viên ({{ $semesterLabel }})</span>
                    </div>
                </div>
            </a>

            {{-- Card 3: Xóa học phần (Link đến DS Xóa HP) --}}
            <a href="{{ route('admin.course_cancellations.index') }}" class="group block">
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Xóa học phần</p>
                        <span class="bg-orange-100 text-orange-600 p-1 rounded">
                            <span class="material-symbols-outlined !text-[15px]">remove_circle_outline</span>
                        </span>
                    </div>
                    <div class="flex items-end gap-2">
                        <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($debtCount) }}</h2>
                        <span class="text-xs text-orange-600 font-medium mb-1">Cần xử lý</span>
                    </div>
                </div>
            </a>

            {{-- Card 4: Biên bản họp (Link đến DS Biên bản) --}}
            <a href="{{ route('admin.minutes.index') }}" class="group block">
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm group-hover:shadow-md group-hover:-translate-y-1 transition-all">
                    <div class="flex justify-between items-start">
                        <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Biên bản họp lớp</p>
                        <span class="bg-indigo-100 text-indigo-600 p-1 rounded">
                            <span class="material-symbols-outlined !text-[15px]">assignment</span>
                        </span>
                    </div>
                    <div class="flex items-end gap-2">
                        <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($minuteCount) }}</h2>
                        <span class="text-xs text-indigo-600 font-medium mb-1">Đã tạo kỳ này</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- BỘ LỌC HỌC KỲ --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-[#1e1e2d] p-3 rounded border border-slate-200 dark:border-slate-700 shadow-sm">
            
            {{-- Form chọn học kỳ (Tự động submit khi chọn) --}}
            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-300 ml-1 hidden sm:inline">Dữ liệu hiển thị:</span>
                <div class="relative flex-1 md:flex-none">
                    <select name="semester_id" onchange="this.form.submit()" 
                            class="w-full md:w-auto appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm rounded pl-3 pr-8 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer font-bold shadow-sm">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ $selectedId == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }})
                                {{ $sem->is_current ? ' - Hiện tại' : '' }}
                            </option>
                        @endforeach
                    </select>
                    <span class="absolute right-2 top-2 pointer-events-none text-slate-500">
                        <span class="material-symbols-outlined !text-[16px]">expand_more</span>
                    </span>
                </div>
            </form>

            <div class="grid grid-cols-2 sm:flex gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.students.create') }}" class="col-span-2 sm:col-span-1 flex items-center justify-center gap-2 px-3 py-1.5 bg-primary text-white text-sm font-bold rounded hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[15px]">add</span>
                    Thêm sinh viên mới
                </a>
            </div>
        </div>

        {{-- DANH SÁCH & WIDGETS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bảng Sinh Viên Bị Cảnh Báo --}}
            <div class="lg:col-span-2 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded flex flex-col shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="font-bold text-slate-800 dark:text-white truncate">Top Sinh viên bị cảnh báo ({{ $semesterLabel }})</h3>
                    <a class="text-xs font-medium text-primary hover:underline" href="{{ route('admin.academic_warnings.index') }}">Xem tất cả</a>
                </div>

                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[600px]">
                        <thead>
                            <tr class="text-xs text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-4 py-3 font-semibold w-24">MSSV</th>
                                <th class="px-4 py-3 font-semibold">Họ và Tên</th>
                                <th class="px-4 py-3 font-semibold">Lớp</th>
                                <th class="px-4 py-3 font-semibold w-16 text-center">GPA</th>
                                <th class="px-4 py-3 font-semibold w-24 text-center">Mức độ</th>
                                <th class="px-4 py-3 font-semibold text-right">Chi tiết</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($studentsToWatch as $student)
                                @php 
                                    $warning = $student->academicWarnings->first();
                                    $level = $warning->warning_level ?? 0;
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                    <td class="px-4 py-2.5 font-mono text-slate-600 dark:text-slate-300 text-xs">{{ $student->student_code }}</td>
                                    <td class="px-4 py-2.5 font-medium text-slate-800 dark:text-slate-200">{{ $student->fullname }}</td>
                                    <td class="px-4 py-2.5 text-slate-500 text-xs">{{ $student->studentClass->code ?? 'N/A' }}</td>
                                    <td class="px-4 py-2.5 text-center font-bold text-slate-700 dark:text-slate-300">{{ $warning->gpa_term ?? '0.0' }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        @if($level == 1)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-50 text-yellow-700 border border-yellow-100">Mức 1</span>
                                        @elseif($level == 2)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-100">Mức 2</span>
                                        @elseif($level >= 3)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-100">Buộc thôi học</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right">
                                        <a href="{{ route('admin.students.show', $student->id) }}" class="text-slate-400 hover:text-primary transition-colors">
                                            <span class="material-symbols-outlined !text-[18px]">visibility</span>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        <span class="material-symbols-outlined !text-[32px] block mb-2 opacity-50">check_circle</span>
                                        Không có cảnh báo nào trong học kỳ này.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- WIDGET BÊN PHẢI --}}
            <div class="flex flex-col gap-6">
                {{-- Widget Thông báo --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 !text-[16px]">campaign</span>
                            Thông báo mới
                        </h3>
                        <a class="text-[10px] text-slate-400 hover:text-primary uppercase font-bold tracking-wider" href="{{ route('admin.notifications.index') }}">Xem hết</a>
                    </div>
                    <div class="flex flex-col gap-4">
                        @forelse($recentNotifications as $notify)
                            <a href="{{ route('admin.notifications.show', $notify->id) }}" class="flex gap-3 items-start pb-3 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors -mx-2 px-2 rounded">
                                <div class="mt-1 min-w-1.5 h-1.5 rounded-full {{ $notify->type == 'urgent' ? 'bg-red-500' : 'bg-primary' }}"></div>
                                <div>
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-snug line-clamp-2">{{ $notify->title }}</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ $notify->created_at->format('d/m/Y') }} • Hệ thống</p>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-slate-400 text-xs py-4">Chưa có thông báo nào.</div>
                        @endforelse
                    </div>
                </div>

                {{-- Widget Lịch --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col shadow-sm flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary !text-[16px]">calendar_month</span>
                            Biên bản họp gần đây
                        </h3>
                        <a href="{{ route('admin.minutes.create') }}" class="p-1 hover:bg-slate-100 rounded text-slate-400"><span class="material-symbols-outlined !text-[15px]">add</span></a>
                    </div>
                    <div class="flex flex-col gap-3">
                        @forelse($upcomingMeetings as $meeting)
                            @php $meetingDate = \Carbon\Carbon::parse($meeting->created_at); @endphp
                            <a href="{{ route('admin.minutes.show', $meeting->id) }}" class="flex gap-3 bg-slate-50 dark:bg-slate-800/50 p-2 rounded border border-slate-100 dark:border-slate-800 hover:border-primary/30 transition-colors">
                                <div class="flex flex-col items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded px-2 py-1 min-w-[45px]">
                                    <span class="text-[10px] font-bold text-red-500 uppercase">{{ $meetingDate->format('M') }}</span>
                                    <span class="text-lg font-bold text-slate-800 dark:text-white leading-none">{{ $meetingDate->format('d') }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $meeting->title }}</p>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="text-[10px] bg-slate-200 dark:bg-slate-600 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">{{ $meetingDate->format('H:i') }}</span>
                                        <span class="text-xs text-slate-500 truncate">{{ $meeting->location ?? 'Online' }}</span>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="text-center text-slate-400 text-xs py-4">Chưa có lịch họp nào.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection