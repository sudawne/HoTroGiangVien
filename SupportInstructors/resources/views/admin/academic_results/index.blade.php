@extends('layouts.admin')
@section('title', 'Quản lý Kết quả học tập')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[28px]">analytics</span>
                    Kết quả Học tập
                </h1>
                <p class="text-sm text-slate-500 mt-1">Theo dõi điểm số, GPA và tín chỉ của sinh viên</p>
            </div>
            <div class="flex gap-2">
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-300 text-slate-700 text-sm font-medium rounded-sm hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">publish</span> Import Điểm
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Excel
                </button>
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-sm shadow-sm mb-6 flex flex-wrap gap-4 items-end">
            <div class="w-full md:w-64">
                <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Tìm kiếm</label>
                <div class="relative">
                    <input type="text" id="live-search-input" value="{{ request('search') }}"
                        placeholder="Nhập tên hoặc MSSV..."
                        class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary">
                    <span
                        class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 !text-[18px]">search</span>
                </div>
            </div>

            <form id="filterForm" class="flex flex-wrap gap-4 flex-1">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Học kỳ</label>
                    <select name="semester_id"
                        class="px-3 py-2 border border-slate-300 text-sm rounded-sm focus:ring-1 focus:ring-primary min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="">-- Tất cả học kỳ --</option>
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1.5">Lớp học</label>
                    <select name="class_id"
                        class="px-3 py-2 border border-slate-300 text-sm rounded-sm focus:ring-1 focus:ring-primary min-w-[150px]"
                        onchange="this.form.submit()">
                        <option value="">-- Toàn trường --</option>
                        @foreach ($classes as $c)
                            <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->code }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        {{-- DATA TABLE --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm relative">
            <div id="table-loading-overlay"
                class="absolute inset-0 bg-white/50 z-10 hidden flex items-center justify-center">
                <span class="animate-spin material-symbols-outlined text-primary !text-[32px]">progress_activity</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th class="px-5 py-3 w-10 text-center">STT</th>
                            <th class="px-5 py-3">Sinh viên</th>
                            <th class="px-5 py-3 text-center">GPA (Hệ 10)</th>
                            <th class="px-5 py-3 text-center">GPA (Hệ 4)</th>
                            <th class="px-5 py-3 text-center">TC Tích lũy</th>
                            <th class="px-5 py-3 text-center">Xếp loại</th>
                            <th class="px-5 py-3 text-center">Điểm RL</th>
                        </tr>
                    </thead>
                    <tbody id="table-body" class="divide-y divide-slate-100">
                        @include('admin.academic_results.partials.table_rows', ['results' => $results])
                    </tbody>
                </table>
            </div>

            <div id="pagination-links" class="px-5 py-4 border-t border-slate-100">
                {{ $results->links() }}
            </div>
        </div>
    </div>

    <script>
        // Script xử lý Live Search (Giống trang Sinh viên)
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('live-search-input');
            const tableBody = document.getElementById('table-body');
            const overlay = document.getElementById('table-loading-overlay');
            let timer;

            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(timer);
                    overlay.classList.remove('hidden');

                    timer = setTimeout(() => {
                        const url = new URL(window.location.href);
                        if (this.value) url.searchParams.set('search', this.value);
                        else url.searchParams.delete('search');
                        url.searchParams.delete('page');

                        window.history.pushState({}, '', url);

                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                tableBody.innerHTML = data.html;
                                document.getElementById('pagination-links').innerHTML = data
                                    .pagination;
                                overlay.classList.add('hidden');
                            });
                    }, 500);
                });
            }
        });
    </script>
@endsection
