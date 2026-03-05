@extends('layouts.admin')

@section('title', 'Quản lý Điểm rèn luyện')

@section('content')
    <div class="max-w-[1400px] mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Điểm rèn luyện</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Quản lý Điểm rèn luyện</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Quản lý, đánh giá và xếp loại điểm rèn luyện sinh viên theo học kỳ.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.training_points.import') }}" class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">upload_file</span>
                    Import Excel
                </a>
                <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- THỐNG KÊ (DYNAMIC DATA) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Tổng --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <p class="text-slate-500 text-xs font-semibold uppercase">Tổng sinh viên</p>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</h2>
            </div>
            {{-- Xuất sắc --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <p class="text-emerald-600 text-xs font-semibold uppercase">Xuất sắc (90+)</p>
                <h2 class="text-3xl font-bold text-emerald-600">{{ $stats['xuatsac'] }}</h2>
            </div>
            {{-- Tốt --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <p class="text-blue-600 text-xs font-semibold uppercase">Tốt (80-89)</p>
                <h2 class="text-3xl font-bold text-blue-600">{{ $stats['tot'] }}</h2>
            </div>
            {{-- Khá --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <p class="text-sky-500 text-xs font-semibold uppercase">Khá (65-79)</p>
                <h2 class="text-3xl font-bold text-sky-500">{{ $stats['kha'] }}</h2>
            </div>
             {{-- Yếu/Chưa xét --}}
             <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <p class="text-red-500 text-xs font-semibold uppercase">Yếu / Chưa xét</p>
                <h2 class="text-3xl font-bold text-red-500">{{ $stats['yeu'] + $stats['chuaxet'] }}</h2>
            </div>
        </div>

        {{-- BỘ LỌC --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary"
                            placeholder="Tìm tên hoặc MSSV..." type="text" />
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="toggleFilterBtn" class="bg-slate-100 dark:bg-slate-800 text-slate-700 border border-slate-200 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">filter_list</span> Bộ lọc nâng cao
                        </button>
                    </div>
                </div>

                <div id="filterPanel" class="{{ request()->hasAny(['semester_id', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 rounded-sm text-sm py-2 pl-3">
                                <option value="">Tất cả học kỳ</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp</label>
                            <select name="class_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 rounded-sm text-sm py-2 pl-3">
                                <option value="">Tất cả lớp</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end">
                            <button type="submit" class="h-[38px] px-4 py-2 text-sm font-medium rounded-sm bg-primary text-white hover:bg-primary/90 w-full">
                                Áp dụng
                            </button>
                        </div>
                         <div class="flex items-end">
                             <a href="{{ route('admin.training_points.index') }}" class="h-[38px] px-4 py-2 text-sm font-medium rounded-sm bg-slate-100 text-slate-600 hover:bg-slate-200 w-full flex items-center justify-center">
                                Xóa lọc
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
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase w-12 text-center">#</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Sinh viên</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">SV Tự ĐG</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Lớp ĐG</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Khoa Duyệt</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Xếp loại</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                        @forelse($trainingPoints as $index => $point)
                            @php
                                // Logic tính màu sắc và xếp loại
                                $score = $point->final_score;
                                $rankName = 'Chưa xét';
                                $rankClass = 'bg-slate-100 text-slate-500';
                                
                                if ($score !== null) {
                                    if ($score >= 90) {
                                        $rankName = 'Xuất sắc';
                                        $rankClass = 'bg-emerald-100 text-emerald-700';
                                    } elseif ($score >= 80) {
                                        $rankName = 'Tốt';
                                        $rankClass = 'bg-blue-100 text-blue-700';
                                    } elseif ($score >= 65) {
                                        $rankName = 'Khá';
                                        $rankClass = 'bg-sky-100 text-sky-700';
                                    } elseif ($score >= 50) {
                                        $rankName = 'Trung bình';
                                        $rankClass = 'bg-orange-100 text-orange-700';
                                    } else {
                                        $rankName = 'Yếu';
                                        $rankClass = 'bg-red-100 text-red-700';
                                    }
                                }
                            @endphp

                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-400">
                                    {{ $trainingPoints->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-800 dark:text-white">{{ $point->student->fullname ?? 'N/A' }}</div>
                                    <div class="text-xs text-slate-500 mt-0.5">MSSV: {{ $point->student->student_code ?? '---' }}</div>
                                    <div class="text-[10px] font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded-sm inline-block mt-1">
                                        {{ $point->student->studentClass->code ?? 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">
                                    {{ $point->self_score ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-center text-slate-500">
                                    {{ $point->class_score ?? '--' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="font-bold {{ $score !== null ? 'text-slate-800' : 'text-slate-400' }} text-base">
                                        {{ $score ?? '0' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 px-2 py-1 rounded-sm text-[10px] font-bold uppercase tracking-wide {{ $rankClass }}">
                                        {{ $rankName }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="#" class="text-slate-400 hover:text-primary transition-colors p-2 rounded-full hover:bg-slate-100" title="Chỉnh sửa">
                                        <span class="material-symbols-outlined !text-[20px]">edit</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-10 text-center text-slate-500 italic">
                                    Không tìm thấy dữ liệu điểm rèn luyện.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $trainingPoints->links() }} 
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggleFilterBtn').addEventListener('click', function() {
            document.getElementById('filterPanel').classList.toggle('hidden');
        });
    </script>
@endsection