@extends('layouts.admin')

@section('title', 'Cảnh báo học tập')

@section('content')
    <div class="max-w-[1400px] mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Cảnh báo học tập</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Cảnh báo học tập</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Quản lý và theo dõi tình trạng học tập của sinh viên.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[16px]">send</span>
                    Gửi thông báo hàng loạt
                </button>
            </div>
        </div>

        {{-- THỐNG KÊ (Dynamic Data) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Card 1: Tổng số --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Tổng số cảnh báo</p>
                    <span class="bg-primary/10 text-primary p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">warning</span></span>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</h2>
            </div>

            {{-- Card 2: Mức 1 --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh báo mức 1</p>
                    <span class="bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-500 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">info</span></span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['level_1'] }}</h2>
                    <span class="text-xs text-slate-400 font-medium mb-1">Đang theo dõi</span>
                </div>
            </div>

            {{-- Card 3: Mức 2 --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh báo mức 2</p>
                    <span class="bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">assignment_late</span></span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['level_2'] }}</h2>
                    <span class="text-xs text-orange-600 font-medium mb-1">Cần gặp cố vấn</span>
                </div>
            </div>

            {{-- Card 4: Thôi học --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Nguy cơ thôi học</p>
                    <span class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">dangerous</span></span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['dropout'] }}</h2>
                    <span class="text-xs text-red-600 font-bold mb-1">Khẩn cấp</span>
                </div>
            </div>
        </div>

        {{-- BỘ LỌC & CÔNG CỤ --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form method="GET" action="{{ route('admin.academic_warnings.index') }}">
                {{-- Hàng 1: Tìm kiếm & Các nút thao tác --}}
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {{-- Tìm kiếm nhanh --}}
                    <div class="relative flex-1 max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm nhanh tên hoặc MSSV..." type="text" />
                    </div>

                    {{-- Nhóm nút thao tác --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Nút Bật/Tắt Bộ lọc --}}
                        <button type="button" id="toggleFilterBtn"
                            class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">filter_list</span>
                            Bộ lọc
                            <span id="filterArrow" class="material-symbols-outlined !text-[16px] transition-transform duration-200">expand_more</span>
                        </button>

                        {{-- Nút Nhập Excel --}}
                        <a href="{{ route('admin.academic_warnings.import') }}"
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>

                        <button type="submit" class="bg-primary hover:bg-indigo-700 text-white text-sm font-medium py-2 px-3 rounded-sm transition-colors flex items-center gap-2 shadow-sm">
                            <span class="material-symbols-outlined !text-[16px]">search</span>
                            <span class="hidden sm:inline">Tìm kiếm</span>
                        </button>
                    </div>
                </div>

                {{-- Hàng 2: Khu vực Lọc nâng cao --}}
                <div id="filterPanel" class="{{ request()->hasAny(['semester_id', 'level', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        {{-- Select Học kỳ --}}
                        <div class="relative">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id" onchange="this.form.submit()"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả học kỳ</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Select Mức độ --}}
                        <div class="relative">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Mức cảnh báo</label>
                            <select name="level" onchange="this.form.submit()"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả mức độ</option>
                                <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>Mức 1</option>
                                <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>Mức 2</option>
                                <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>Buộc thôi học</option>
                            </select>
                        </div>

                        {{-- Select Lớp --}}
                        <div class="relative">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp sinh hoạt</label>
                            <select name="class_id" onchange="this.form.submit()"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả lớp</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                         <div class="flex items-end">
                            <a href="{{ route('admin.academic_warnings.index') }}" class="w-full text-center bg-gray-100 hover:bg-gray-200 text-slate-700 text-sm font-medium py-2 px-4 rounded-sm transition-colors h-[38px] flex items-center justify-center">
                                Xóa bộ lọc
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div class="bg-white dark:bg-[#1e1e2d] rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">MSSV</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Họ và tên</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lớp</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">GPA</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Nợ tín</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Mức cảnh báo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lý do</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        @forelse($warnings as $warning)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-primary font-mono">
                                    {{ $warning->student->student_code }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- Avatar giả lập lấy 2 chữ cái đầu --}}
                                        <div class="w-8 h-8 rounded-sm bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                            {{ substr(strtoupper($warning->student->fullname), 0, 1) }}
                                        </div>
                                        <div class="text-sm font-medium dark:text-slate-200">
                                            {{ $warning->student->fullname }}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
                                    {{ $warning->student->class->code ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center font-bold {{ $warning->gpa_term < 2.0 ? 'text-red-500' : 'text-slate-700' }}">
                                    {{ $warning->gpa_term }}
                                </td>
                                <td class="px-6 py-4 text-sm text-center text-slate-600">
                                    {{ $warning->credits_owed }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($warning->warning_level == 1)
                                        <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-yellow-100 text-yellow-700">Mức 1</span>
                                    @elseif($warning->warning_level == 2)
                                        <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-orange-100 text-orange-700">Mức 2</span>
                                    @elseif($warning->warning_level >= 3)
                                        <span class="px-2.5 py-0.5 rounded-sm text-xs font-bold bg-red-200 text-red-800">Buộc thôi học</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-slate-100 text-slate-600">Khác</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500 max-w-[150px] truncate" title="{{ $warning->reason }}">
                                    {{ $warning->reason }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button class="p-1.5 text-slate-400 hover:text-primary transition-colors" title="Xem chi tiết">
                                            <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-8 text-center text-slate-500">
                                    Không có dữ liệu cảnh báo nào phù hợp.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang thật --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $warnings->links() }} 
                {{-- Lưu ý: Bạn cần publish pagination view của Laravel nếu muốn style giống hệt Tailwind, 
                     mặc định nó dùng Tailwind nên sẽ khá ổn --}}
            </div>
        </div>
    </div>

    {{-- SCRIPT XỬ LÝ ĐÓNG MỞ BỘ LỌC --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFilterBtn');
            const filterPanel = document.getElementById('filterPanel');
            const arrow = document.getElementById('filterArrow');

            toggleBtn.addEventListener('click', function() {
                filterPanel.classList.toggle('hidden');
                if (filterPanel.classList.contains('hidden')) {
                    arrow.style.transform = 'rotate(0deg)';
                    toggleBtn.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                } else {
                    arrow.style.transform = 'rotate(180deg)';
                    toggleBtn.classList.add('bg-slate-200', 'dark:bg-slate-700');
                }
            });
        });
    </script>
@endsection