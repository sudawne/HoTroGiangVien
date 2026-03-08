@extends('layouts.admin')

@section('title', 'Danh sách Xóa học phần')

@section('content')
    <div class="max-w-[1400px] mx-auto" x-data="{ showExportModal: false }">

        {{-- HEADER & BREADCRUMB --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Xóa học phần</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sinh viên bị Xóa học phần</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Danh sách sinh viên bị hủy môn do nợ học phí hoặc vi phạm quy chế.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showExportModal = true" class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- THỐNG KÊ (Đã bỏ phần tiền nợ) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            {{-- Card 1: Tổng số --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Tổng lượt xóa</p>
                    <span class="bg-red-50 text-red-600 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">remove_circle</span></span>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($stats['total'] ?? 0) }}</h2>
            </div>

            {{-- Card 2: Sinh viên --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Số SV bị ảnh hưởng</p>
                    <span class="bg-blue-50 text-blue-600 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">person_off</span></span>
                </div>
                <h2 class="text-3xl font-bold text-primary">{{ number_format($stats['unique_students'] ?? 0) }}</h2>
            </div>

            {{-- Card 3: Môn học (Thay thế cho Tiền nợ) --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Môn học liên quan</p>
                    <span class="bg-orange-50 text-orange-600 p-1 rounded-sm"><span class="material-symbols-outlined !text-[15px]">menu_book</span></span>
                </div>
                {{-- Nếu bạn có thống kê số môn unique thì hiện, ko thì hiện tổng --}}
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($cancellations->total()) }}</h2>
            </div>
        </div>

        {{-- BỘ LỌC & CÔNG CỤ --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input id="searchInput" name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm tên SV, MSSV hoặc tên môn..." type="text" autocomplete="off" />
                        <span id="searchSpinner" class="material-symbols-outlined absolute right-3 top-2.5 text-primary !text-[16px] animate-spin hidden">sync</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select name="semester_id" class="live-filter bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm focus:ring-primary focus:border-primary">
                            <option value="">-- Tất cả học kỳ --</option>
                            @foreach($semesters as $sem)
                                <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->name }} ({{ $sem->academic_year }})
                                </option>
                            @endforeach
                        </select>
                        
                        <button type="button" onclick="window.location.href='{{ route('admin.course_cancellations.index') }}'" 
                                class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[18px]">filter_alt_off</span> Xóa lọc
                        </button>
                        <a href="{{ route('admin.course_cancellations.import') }}" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div class="bg-white dark:bg-[#1e1e2d] rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative">
            {{-- Overlay Loading --}}
            <div id="tableOverlay" class="absolute inset-0 bg-white/50 dark:bg-black/20 z-10 hidden"></div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Sinh viên</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lớp</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Môn bị xóa</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Học kỳ</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Lý do</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        @include('admin.course_cancellations.partials.table_rows')
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $cancellations->links() }}
            </div>
        </div>

        {{-- MODAL EXPORT --}}
        <div x-show="showExportModal" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            style="display: none;">
            
            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-xl w-full max-w-lg overflow-hidden" @click.away="showExportModal = false">
                <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">print_connect</span>
                        Xuất báo cáo Xóa học phần
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route('admin.course_cancellations.export') }}" method="GET" class="p-6">
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phạm vi Học kỳ</label>
                            <select name="semester_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary">
                                <option value="">-- Tất cả học kỳ --</option>
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Định dạng file</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="format" value="excel" class="peer sr-only" checked>
                                    <div class="p-3 rounded border-2 border-slate-200 hover:bg-slate-50 peer-checked:border-green-500 peer-checked:bg-green-50 flex flex-col items-center gap-1 transition-all">
                                        <span class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-green-600">table_view</span>
                                        <span class="text-xs font-bold text-slate-600 peer-checked:text-green-700">Excel (.xlsx)</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="format" value="pdf" class="peer sr-only">
                                    <div class="p-3 rounded border-2 border-slate-200 hover:bg-slate-50 peer-checked:border-red-500 peer-checked:bg-red-50 flex flex-col items-center gap-1 transition-all">
                                        <span class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-red-600">picture_as_pdf</span>
                                        <span class="text-xs font-bold text-slate-600 peer-checked:text-red-700">PDF (.pdf)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showExportModal = false" class="px-4 py-2 border rounded-sm text-sm font-medium hover:bg-slate-50">Hủy</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-sm text-sm font-bold hover:bg-blue-700 flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[18px]">download</span> Tải xuống
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- SCRIPT LIVE SEARCH --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const tableBody = document.getElementById('tableBody');
            const searchSpinner = document.getElementById('searchSpinner');
            const tableOverlay = document.getElementById('tableOverlay');
            const liveFilters = document.querySelectorAll('.live-filter');
            let timeout = null;

            function fetchResults() {
                searchSpinner.classList.remove('hidden');
                tableOverlay.classList.remove('hidden');

                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData).toString();
                const newUrl = `${window.location.pathname}?${params}`;
                window.history.pushState({path: newUrl}, '', newUrl);

                fetch(newUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => {
                    tableBody.innerHTML = html;
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 500);
            });

            liveFilters.forEach(select => {
                select.addEventListener('change', fetchResults);
            });
        });
    </script>
@endsection