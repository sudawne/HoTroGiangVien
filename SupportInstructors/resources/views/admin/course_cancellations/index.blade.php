@extends('layouts.admin')

@section('title', 'Danh sách Xóa học phần')

@section('content')
    {{-- THÊM THƯ VIỆN CHART.JS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="max-w-[1400px] mx-auto" x-data="{ showExportModal: false }">

        {{-- HEADER & BREADCRUMB --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors"
                                href="{{ route($routePrefix . 'dashboard') }}">Trang chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Xóa học phần</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Sinh viên bị Xóa học phần</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Danh sách sinh viên bị hủy môn do nợ học phí
                    hoặc vi phạm quy chế.</p>
            </div>
            <div class="flex items-center gap-3">
                <button @click="showExportModal = true"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- KHU VỰC THỐNG KÊ & BIỂU ĐỒ --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

            {{-- Cột trái: Thẻ thống kê & Bộ lọc --}}
            <div class="lg:col-span-2 flex flex-col gap-4">

                {{-- Form chọn lớp (Có giữ lại URL parameters hiện tại) --}}
                <div
                    class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200 flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[18px] text-primary">pie_chart</span>
                        Thống kê tỷ lệ nợ học phí
                    </h3>
                    <form method="GET" id="classFilterForm" class="w-full sm:w-auto">
                        @if (request('semester_id'))
                            <input type="hidden" name="semester_id" value="{{ request('semester_id') }}">
                        @endif
                        @if (request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif

                        <select name="class_id" onchange="document.getElementById('classFilterForm').submit()"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm focus:ring-primary focus:border-primary min-w-[200px] shadow-sm cursor-pointer">
                            <option value="all">-- Tất cả lớp quản lý --</option>
                            @foreach ($myClasses as $cls)
                                <option value="{{ $cls->id }}" {{ $selectedClass == $cls->id ? 'selected' : '' }}>
                                    {{ $cls->code }} - {{ $cls->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                {{-- Các thẻ số liệu --}}
                <div class="grid grid-cols-2 gap-4 h-full">
                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-5 flex flex-col justify-center shadow-sm">
                        <div class="flex justify-between items-start mb-2">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wide">Sĩ số
                                sinh viên</p>
                            <span
                                class="bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 p-1.5 rounded-sm"><span
                                    class="material-symbols-outlined !text-[18px]">groups</span></span>
                        </div>
                        <h2 class="text-3xl font-extrabold text-slate-800 dark:text-white">
                            {{ number_format($totalStudents) }} <span class="text-sm font-medium text-slate-500">SV</span>
                        </h2>
                    </div>

                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-5 flex flex-col justify-center shadow-sm relative overflow-hidden">
                        <div class="flex justify-between items-start mb-2 relative z-10">
                            <p class="text-slate-500 dark:text-slate-400 text-xs font-bold uppercase tracking-wide">SV Nợ
                                học phí</p>
                            <span class="bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 p-1.5 rounded-sm"><span
                                    class="material-symbols-outlined !text-[18px]">money_off</span></span>
                        </div>
                        <div class="flex items-end gap-2 relative z-10">
                            <h2 class="text-3xl font-extrabold text-red-600 dark:text-red-400">
                                {{ number_format($debtStudentsCount) }}</h2>
                            <span
                                class="text-sm font-bold text-red-500 dark:text-red-400 mb-1">({{ $debtPercentage }}%)</span>
                        </div>
                        <div class="absolute bottom-0 left-0 h-1 bg-red-500 transition-all duration-1000"
                            style="width: {{ $debtPercentage }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Biểu đồ tròn --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] p-5 rounded-sm border border-slate-200 dark:border-slate-700 shadow-sm flex flex-col items-center justify-center">
                <div class="relative w-full max-w-[180px] aspect-square">
                    <canvas id="tuitionChart"></canvas>
                    {{-- Chữ hiển thị ở giữa tâm vòng tròn --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-1">
                        <span
                            class="text-2xl font-black {{ $debtPercentage > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400' }}">{{ $debtPercentage }}%</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">Nợ phí</span>
                    </div>
                </div>
                <div class="flex gap-4 mt-4 text-[11px] font-bold text-slate-500 dark:text-slate-400">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-500"></span> Nợ phí</div>
                    <div class="flex items-center gap-1.5"><span
                            class="w-3 h-3 rounded-sm bg-slate-200 dark:bg-slate-600"></span> An toàn</div>
                </div>
            </div>
        </div>

        {{-- BỘ LỌC & CÔNG CỤ --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form id="filterForm" method="GET">
                @if (request('class_id'))
                    <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                @endif

                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div class="relative flex-1 max-w-md">
                        <span
                            class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input id="searchInput" name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm tên SV, MSSV hoặc tên môn..." type="text" autocomplete="off" />
                        <span id="searchSpinner"
                            class="material-symbols-outlined absolute right-3 top-2.5 text-primary !text-[16px] animate-spin hidden">sync</span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        <select name="semester_id"
                            class="live-filter bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm focus:ring-primary focus:border-primary">
                            <option value="">-- Tất cả học kỳ --</option>
                            @foreach ($semesters as $sem)
                                <option value="{{ $sem->id }}"
                                    {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                    {{ $sem->name }} ({{ $sem->academic_year }})
                                </option>
                            @endforeach
                        </select>

                        <button type="button"
                            onclick="window.location.href='{{ route($routePrefix . 'course_cancellations.index') }}'"
                            class="bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-all flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[18px]">filter_alt_off</span> Xóa lọc
                        </button>
                        <a href="{{ route($routePrefix . 'course_cancellations.import') }}"
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative">
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
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">

            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-xl w-full max-w-lg overflow-hidden"
                @click.away="showExportModal = false">
                <div
                    class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">print_connect</span>
                        Xuất báo cáo Xóa học phần
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form action="{{ route($routePrefix . 'course_cancellations.export') }}" method="GET" class="p-6">
                    @if (request('class_id'))
                        <input type="hidden" name="class_id" value="{{ request('class_id') }}">
                    @endif

                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Phạm vi Học
                                kỳ</label>
                            <select name="semester_id"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary">
                                <option value="">-- Tất cả học kỳ --</option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem->id }}"
                                        {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Định dạng
                                file</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="format" value="excel" class="peer sr-only" checked>
                                    <div
                                        class="p-3 rounded border-2 border-slate-200 hover:bg-slate-50 peer-checked:border-green-500 peer-checked:bg-green-50 flex flex-col items-center gap-1 transition-all">
                                        <span
                                            class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-green-600">table_view</span>
                                        <span class="text-xs font-bold text-slate-600 peer-checked:text-green-700">Excel
                                            (.xlsx)</span>
                                    </div>
                                </label>
                                <label class="cursor-pointer relative">
                                    <input type="radio" name="format" value="pdf" class="peer sr-only">
                                    <div
                                        class="p-3 rounded border-2 border-slate-200 hover:bg-slate-50 peer-checked:border-red-500 peer-checked:bg-red-50 flex flex-col items-center gap-1 transition-all">
                                        <span
                                            class="material-symbols-outlined text-2xl text-slate-400 peer-checked:text-red-600">picture_as_pdf</span>
                                        <span class="text-xs font-bold text-slate-600 peer-checked:text-red-700">PDF
                                            (.pdf)</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showExportModal = false"
                            class="px-4 py-2 border rounded-sm text-sm font-medium hover:bg-slate-50">Hủy</button>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-sm text-sm font-bold hover:bg-blue-700 flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[18px]">download</span> Tải xuống
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- SCRIPT CHẠY BIỂU ĐỒ TRÒN (CHART.JS) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('tuitionChart').getContext('2d');

            // Render dữ liệu phần trăm từ Backend xuống JS
            const debtPercent = {{ $debtPercentage }};
            const cleanPercent = {{ $cleanPercentage }};

            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Nợ học phí', 'Đã hoàn thành'],
                    datasets: [{
                        data: [debtPercent, cleanPercent],
                        backgroundColor: [
                            '#ef4444', // Màu đỏ (Tailwind red-500)
                            '#e2e8f0' // Màu xám nhạt (Tailwind slate-200)
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '78%', // Khoảng trống ở giữa, làm vòng biểu đồ mỏng lại
                    plugins: {
                        legend: {
                            display: false
                        }, // Ẩn legend mặc định
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' ' + context.label + ': ' + context.parsed + '%';
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
        });
    </script>

    {{-- SCRIPT LIVE SEARCH VÀ LIVE FILTER --}}
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
                window.history.pushState({
                    path: newUrl
                }, '', newUrl);

                fetch(newUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
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
