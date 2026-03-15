@extends('layouts.admin')

@section('title', 'Quản lý Điểm rèn luyện')

@section('content')
    {{-- THÊM THƯ VIỆN CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Khai báo x-data bọc ngoài cùng để Alpine.js quản lý modal --}}
    <div class="max-w-[1400px] mx-auto" x-data="{ showExportModal: false }">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors"
                                href="{{ route($routePrefix . 'dashboard') }}">Trang
                                chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Điểm rèn luyện</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Quản lý Điểm rèn luyện</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Quản lý, đánh giá và xếp loại điểm rèn luyện
                    sinh viên theo học kỳ.</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showExportModal = true"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- LẤY DỮ LIỆU AN TOÀN CHO BIỂU ĐỒ --}}
        @php
            $total = $stats['total'] ?? 0;
            $xs = $stats['xuatsac'] ?? 0;
            $tot = $stats['tot'] ?? 0;
            $kha = $stats['kha'] ?? 0;
            $tb = $stats['trungbinh'] ?? ($stats['tb'] ?? 0);
            $yeu = ($stats['yeu'] ?? 0) + ($stats['chuaxet'] ?? 0);
        @endphp

        {{-- KHU VỰC THỐNG KÊ & BIỂU ĐỒ --}}
        <div class="flex flex-col xl:flex-row gap-6 mb-6">
            
            {{-- Cột trái: Các thẻ Card số liệu --}}
            <div class="w-full xl:w-3/4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                {{-- Card 1: Tổng sinh viên --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-[104px] shadow-sm">
                    <div class="flex justify-between items-start">
                        <p class="text-slate-500 text-[11px] font-bold uppercase">Tổng sinh viên</p>
                        <span class="bg-indigo-50 text-indigo-600 p-1 rounded-sm border border-indigo-100">
                            <span class="material-symbols-outlined !text-[16px]">group</span>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ number_format($total) }}</h2>
                </div>

                {{-- Card 2: Xuất sắc --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-[104px] shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <p class="text-slate-500 text-[11px] font-bold uppercase">Xuất sắc (90+)</p>
                        <span class="bg-emerald-50 text-emerald-600 p-1 rounded-sm border border-emerald-100">
                            <span class="material-symbols-outlined !text-[16px]">military_tech</span>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-emerald-600 relative z-10">{{ number_format($xs) }}</h2>
                    <div class="absolute bottom-0 left-0 h-1 bg-emerald-500" style="width: {{ $total > 0 ? ($xs/$total)*100 : 0 }}%"></div>
                </div>

                {{-- Card 3: Tốt --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-[104px] shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <p class="text-slate-500 text-[11px] font-bold uppercase">Tốt (80-89)</p>
                        <span class="bg-blue-50 text-blue-600 p-1 rounded-sm border border-blue-100">
                            <span class="material-symbols-outlined !text-[16px]">thumb_up</span>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-blue-600 relative z-10">{{ number_format($tot) }}</h2>
                    <div class="absolute bottom-0 left-0 h-1 bg-blue-500" style="width: {{ $total > 0 ? ($tot/$total)*100 : 0 }}%"></div>
                </div>

                {{-- Card 4: Khá --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-[104px] shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <p class="text-slate-500 text-[11px] font-bold uppercase">Khá (65-79)</p>
                        <span class="bg-sky-50 text-sky-600 p-1 rounded-sm border border-sky-100">
                            <span class="material-symbols-outlined !text-[16px]">sentiment_satisfied</span>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-sky-500 relative z-10">{{ number_format($kha) }}</h2>
                    <div class="absolute bottom-0 left-0 h-1 bg-sky-500" style="width: {{ $total > 0 ? ($kha/$total)*100 : 0 }}%"></div>
                </div>

                {{-- Card 5: Yếu/Chưa xét --}}
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-[104px] shadow-sm relative overflow-hidden">
                    <div class="flex justify-between items-start relative z-10">
                        <p class="text-slate-500 text-[11px] font-bold uppercase">Yếu/Kém/CX</p>
                        <span class="bg-red-50 text-red-600 p-1 rounded-sm border border-red-100">
                            <span class="material-symbols-outlined !text-[16px]">warning</span>
                        </span>
                    </div>
                    <h2 class="text-3xl font-bold text-red-500 relative z-10">{{ number_format($yeu) }}</h2>
                    <div class="absolute bottom-0 left-0 h-1 bg-red-500" style="width: {{ $total > 0 ? ($yeu/$total)*100 : 0 }}%"></div>
                </div>
            </div>

            {{-- Cột phải: Biểu đồ phần trăm --}}
            <div class="w-full xl:w-1/4 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 shadow-sm flex flex-col items-center justify-center">
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 mb-3 w-full text-center flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined !text-[18px] text-primary">pie_chart</span>
                    Tỷ lệ xếp loại ĐRL
                </h3>
                
                @if($total > 0)
                    <div class="relative w-full max-w-[130px] aspect-square">
                        <canvas id="rankChart"></canvas>
                    </div>
                    <div class="flex flex-wrap justify-center gap-2.5 mt-4 text-[10px] font-bold text-slate-500 dark:text-slate-400">
                        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-emerald-500"></span> X.Sắc</div>
                        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-blue-500"></span> Tốt</div>
                        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-sky-400"></span> Khá</div>
                        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-orange-400"></span> T.Bình</div>
                        <div class="flex items-center gap-1"><span class="w-2.5 h-2.5 rounded-sm bg-red-500"></span> Yếu</div>
                    </div>
                @else
                    <div class="flex-1 flex flex-col items-center justify-center text-slate-400">
                        <span class="material-symbols-outlined text-4xl mb-2 opacity-50">data_alert</span>
                        <span class="text-xs">Chưa có dữ liệu</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- BỘ LỌC & CÔNG CỤ --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <span
                            class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input id="searchInput" name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm nhanh tên hoặc MSSV..." type="text" autocomplete="off" />
                        <span id="searchSpinner"
                            class="material-symbols-outlined absolute right-3 top-2.5 text-primary !text-[16px] animate-spin hidden">sync</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="toggleFilterBtn"
                            class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">filter_list</span> Bộ lọc
                        </button>

                        <a href="{{ route($routePrefix . 'training_points.import') }}"
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>

                <div id="filterPanel"
                    class="{{ request()->hasAny(['semester_id', 'rank', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">

                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả học kỳ</option>
                                @if (isset($semesters))
                                    @foreach ($semesters as $sem)
                                        <option value="{{ $sem->id }}"
                                            {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Xếp loại</label>
                            <select name="rank"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả xếp loại</option>
                                <option value="xuatsac" {{ request('rank') == 'xuatsac' ? 'selected' : '' }}>Xuất sắc
                                    (90-100)</option>
                                <option value="tot" {{ request('rank') == 'tot' ? 'selected' : '' }}>Tốt (80-89)
                                </option>
                                <option value="kha" {{ request('rank') == 'kha' ? 'selected' : '' }}>Khá (65-79)
                                </option>
                                <option value="trungbinh" {{ request('rank') == 'trungbinh' ? 'selected' : '' }}>Trung bình
                                    (50-64)</option>
                                <option value="yeu" {{ request('rank') == 'yeu' ? 'selected' : '' }}>Yếu/Kém (<50)
                                </option>
                            </select>
                        </div>

                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp</label>
                            <select name="class_id"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả lớp</option>
                                @if (isset($classes))
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->code }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="button" onclick="resetFilters()"
                                class="h-[28.6px] px-4 py-2 text-sm font-medium rounded-sm transition-all flex items-center gap-2
                                        bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-400
                                        hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-600 dark:hover:text-red-400 hover:border-red-200
                                        w-full md:w-auto justify-center md:justify-start">
                                <span class="material-symbols-outlined !text-[18px]">filter_alt_off</span>
                                <span>Xóa lọc</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div class="overflow-x-auto relative min-h-[300px] bg-white">
            <div id="tableOverlay"
                class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 z-10 hidden transition-opacity flex items-center justify-center">
                <span class="material-symbols-outlined animate-spin text-primary text-3xl">sync</span>
            </div>

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
                <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @include('admin.training_points.partials.table_rows')
                </tbody>
            </table>
        </div>

        {{-- MODAL XUẤT BÁO CÁO ALPINEJS --}}
        <div x-show="showExportModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;" x-cloak>

            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-xl w-full max-w-lg overflow-hidden"
                @click.away="showExportModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <div
                    class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">print_connect</span>
                        Tùy chọn Xuất báo cáo (ĐRL)
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route($routePrefix . 'training_points.export') }}" method="GET" class="p-6">
                    <div class="space-y-4 mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">1. Phạm vi dữ liệu</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Học kỳ</label>
                                <select name="semester_id"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    @if (isset($semesters))
                                        @foreach ($semesters as $sem)
                                            <option value="{{ $sem->id }}">{{ $sem->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Xếp loại</label>
                                <select name="rank"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    <option value="xuatsac">Xuất sắc (90-100)</option>
                                    <option value="tot">Tốt (80-89)</option>
                                    <option value="kha">Khá (65-79)</option>
                                    <option value="trungbinh">Trung bình (50-64)</option>
                                    <option value="yeu">Yếu/Kém (<50)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lớp sinh hoạt</label>
                            <select name="class_id"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                <option value="">-- Tất cả các lớp --</option>
                                @if (isset($classes))
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->code }} - {{ $class->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">2. Định dạng file</p>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="excel" class="peer sr-only" checked>
                                <div
                                    class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-green-500 peer-checked:bg-green-50/50 dark:peer-checked:bg-green-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-green-600 peer-checked:text-green-600 transition-colors">table_view</span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-green-700">Xuất Excel</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-green-500 peer-checked:border-green-500 transition-colors"></div>
                            </label>

                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="pdf" class="peer sr-only">
                                <div
                                    class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-red-500 peer-checked:bg-red-50/50 dark:peer-checked:bg-red-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-red-600 peer-checked:text-red-600 transition-colors">picture_as_pdf</span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-red-700">Xuất PDF</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-red-500 peer-checked:border-red-500 transition-colors"></div>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showExportModal = false"
                            class="px-4 py-2 bg-white border border-slate-300 rounded-sm text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-sm text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all transform active:scale-95">
                            <span class="material-symbols-outlined !text-[18px]">download</span> Tải xuống
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- SCRIPT RENDER BIỂU ĐỒ TRÒN --}}
    @if($total > 0)
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('rankChart');
            if (ctx) {
                new Chart(ctx.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Xuất sắc', 'Tốt', 'Khá', 'Trung bình', 'Yếu/Kém/CX'],
                        datasets: [{
                            data: [{{ $xs }}, {{ $tot }}, {{ $kha }}, {{ $tb }}, {{ $yeu }}],
                            backgroundColor: [
                                '#10b981', // Emerald 500
                                '#3b82f6', // Blue 500
                                '#38bdf8', // Sky 400
                                '#fb923c', // Orange 400
                                '#ef4444'  // Red 500
                            ],
                            borderWidth: 0,
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        cutout: '72%', // Độ mỏng vòng tròn
                        plugins: {
                            legend: { display: false }, // Ẩn chú thích mặc định
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        let val = context.parsed;
                                        let percent = total > 0 ? Math.round((val / total) * 100) : 0;
                                        return ' ' + context.label + ': ' + val + ' SV (' + percent + '%)';
                                    }
                                }
                            }
                        },
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        }
                    }
                });
            }
        });
    </script>
    @endif

    {{-- SCRIPT LIVE SEARCH VÀ LIVE FILTER GIỮ NGUYÊN --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="search"]');
            const filterForm = document.getElementById('filterForm');
            const tableBody = document.getElementById('tableBody');
            const tableOverlay = document.getElementById('tableOverlay');
            const liveFilters = document.querySelectorAll('.live-filter');
            let timeout = null;

            // Hàm gọi Ajax cập nhật danh sách
            function fetchResults(url) {
                tableOverlay.classList.remove('hidden');

                if (!url) {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData).toString();
                    url = "{{ route($routePrefix . 'training_points.index') }}?" + params;
                }

                window.history.pushState(null, '', url);

                fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableBody.innerHTML = html;
                        tableOverlay.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error('Lỗi:', error);
                        tableOverlay.classList.add('hidden');
                    });
            }
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    fetchResults();
                }, 500);
            });
            liveFilters.forEach(select => {
                select.addEventListener('change', function() {
                    filterForm.submit(); 
                });
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    const url = e.target.closest('.pagination a').href;
                    fetchResults(url);
                }
            });

            document.getElementById('toggleFilterBtn').addEventListener('click', function() {
                document.getElementById('filterPanel').classList.toggle('hidden');
            });

            window.resetFilters = function() {
                window.location.href = "{{ route($routePrefix . 'training_points.index') }}";
            }
        });
    </script>
@endsection