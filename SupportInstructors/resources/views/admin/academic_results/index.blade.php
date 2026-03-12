@extends('layouts.admin')

@section('title', 'Quản lý Kết quả học tập')

@section('content')
    <div class="max-w-[1400px] mx-auto" x-data="{ showExportModal: false }">

        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <x-page-header title="Quản lý Kết quả học tập"
                description="Theo dõi TBHK hệ 10, hệ 4 và xếp loại học lực của sinh viên." :routePrefix="$routePrefix"
                :breadcrumbs="[['label' => 'Kết quả học tập']]" />

            <div class="flex items-center gap-2">
                <button @click="showExportModal = true"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- THỐNG KÊ (STATS CARDS) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Tổng --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Tổng sinh viên</p>
                    <span class="bg-indigo-50 text-indigo-600 p-1 rounded-sm border border-indigo-100">
                        <span class="material-symbols-outlined !text-[16px]">group</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</h2>
            </div>

            {{-- Xuất sắc/Giỏi --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Xuất sắc / Giỏi</p>
                    <span class="bg-emerald-50 text-emerald-600 p-1 rounded-sm border border-emerald-100">
                        <span class="material-symbols-outlined !text-[16px]">stars</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-emerald-600">{{ $stats['xuatsac'] }}</h2>
            </div>

            {{-- Khá --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Khá</p>
                    <span class="bg-blue-50 text-blue-600 p-1 rounded-sm border border-blue-100">
                        <span class="material-symbols-outlined !text-[16px]">trending_up</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-blue-600">{{ $stats['kha'] }}</h2>
            </div>

            {{-- Trung bình --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Trung bình</p>
                    <span class="bg-orange-50 text-orange-600 p-1 rounded-sm border border-orange-100">
                        <span class="material-symbols-outlined !text-[16px]">remove</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-orange-500">{{ $stats['tb'] }}</h2>
            </div>

            {{-- Yếu/Kém --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Yếu / Kém</p>
                    <span class="bg-red-50 text-red-600 p-1 rounded-sm border border-red-100">
                        <span class="material-symbols-outlined !text-[16px]">warning</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-red-500">{{ $stats['yeu'] }}</h2>
            </div>
        </div>

        {{-- BỘ LỌC & TOOLBAR --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {{-- Search --}}
                    <div class="relative flex-1 max-w-md">
                        <span
                            class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input id="searchInput" name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm nhanh tên hoặc MSSV..." type="text" autocomplete="off" />
                        <span id="searchSpinner"
                            class="material-symbols-outlined absolute right-3 top-2.5 text-primary !text-[16px] animate-spin hidden">sync</span>
                    </div>

                    {{-- Nút Filter --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="toggleFilterBtn"
                            class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">filter_list</span> Bộ lọc
                        </button>
                        <a href="{{ route($routePrefix . 'academic_results.import') }}"
                            class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>

                {{-- Filter Panel--}}
                <div id="filterPanel"
                    class="{{ request()->hasAny(['semester_id', 'classification', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">

                        {{-- Lọc Học kỳ --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả học kỳ</option>
                                @foreach ($semesters as $sem)
                                    <option value="{{ $sem->id }}"
                                        {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Lọc Xếp loại --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Xếp loại</label>
                            <select name="classification"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả xếp loại</option>
                                <option value="Xuất sắc" {{ request('classification') == 'Xuất sắc' ? 'selected' : '' }}>
                                    Xuất sắc</option>
                                <option value="Giỏi" {{ request('classification') == 'Giỏi' ? 'selected' : '' }}>Giỏi
                                </option>
                                <option value="Khá" {{ request('classification') == 'Khá' ? 'selected' : '' }}>Khá
                                </option>
                                <option value="Trung bình"
                                    {{ request('classification') == 'Trung bình' ? 'selected' : '' }}>Trung bình</option>
                                <option value="Yếu" {{ request('classification') == 'Yếu' ? 'selected' : '' }}>Yếu
                                </option>
                                <option value="Kém" {{ request('classification') == 'Kém' ? 'selected' : '' }}>Kém
                                </option>
                            </select>
                        </div>

                        {{-- Lọc Lớp --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp</label>
                            <select name="class_id"
                                class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả lớp</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                        {{ $class->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Nút Xóa lọc --}}
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
        <div class="overflow-x-auto relative min-h-[300px] bg-white rounded-sm border border-slate-200 shadow-sm">
            <div id="tableOverlay"
                class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 z-10 hidden transition-opacity flex items-center justify-center">
                <span class="material-symbols-outlined animate-spin text-primary text-3xl">sync</span>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase w-12 text-center">#</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Sinh viên</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lớp</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">TBHK (10)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">TBHK (4)</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Xếp loại</th>
                        <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Tác vụ</th>
                    </tr>
                </thead>
                <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @include('admin.academic_results.partials.table_rows')
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $results->links() }}
        </div>

        {{-- MODAL XUẤT BÁO CÁO (ĐƯỢC CHÈN VÀO ĐÂY) --}}
        <div x-show="showExportModal"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;" x-cloak>

            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-xl w-full max-w-lg overflow-hidden"
                @click.away="showExportModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                {{-- Header Modal --}}
                <div
                    class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">print_connect</span>
                        Tùy chọn Xuất báo cáo (Kết quả HT)
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Form Xuất Báo Cáo --}}
                <form action="{{ route($routePrefix . 'academic_results.export') }}" method="GET" class="p-6">

                    {{-- Các tiêu chí lọc --}}
                    <div class="space-y-4 mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">1. Phạm vi dữ liệu</p>

                        <div class="grid grid-cols-2 gap-4">
                            {{-- Chọn Học kỳ --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Học
                                    kỳ</label>
                                <select name="semester_id"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    @foreach ($semesters as $sem)
                                        <option value="{{ $sem->id }}"
                                            {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->name }} ({{ $sem->academic_year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chọn Xếp loại --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Xếp
                                    loại</label>
                                <select name="classification"
                                    class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    <option value="Xuất sắc">Xuất sắc</option>
                                    <option value="Giỏi">Giỏi</option>
                                    <option value="Khá">Khá</option>
                                    <option value="Trung bình">Trung bình</option>
                                    <option value="Yếu">Yếu</option>
                                    <option value="Kém">Kém</option>
                                </select>
                            </div>
                        </div>

                        {{-- Chọn Lớp --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lớp sinh
                                hoạt</label>
                            <select name="class_id"
                                class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                <option value="">-- Tất cả các lớp --</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->code }} - {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Chọn định dạng File --}}
                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">2. Định dạng file</p>
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Option Excel --}}
                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="excel" class="peer sr-only" checked>
                                <div
                                    class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-green-500 peer-checked:bg-green-50/50 dark:peer-checked:bg-green-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span
                                        class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-green-600 peer-checked:text-green-600 transition-colors">table_view</span>
                                    <span
                                        class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-green-700">Xuất
                                        Excel</span>
                                </div>
                                <div
                                    class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-green-500 peer-checked:border-green-500 transition-colors">
                                </div>
                            </label>

                            {{-- Option PDF --}}
                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="pdf" class="peer sr-only">
                                <div
                                    class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-red-500 peer-checked:bg-red-50/50 dark:peer-checked:bg-red-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span
                                        class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-red-600 peer-checked:text-red-600 transition-colors">picture_as_pdf</span>
                                    <span
                                        class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-red-700">Xuất
                                        PDF</span>
                                </div>
                                <div
                                    class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-red-500 peer-checked:border-red-500 transition-colors">
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchSpinner = document.getElementById('searchSpinner');
            const tableBody = document.getElementById('tableBody');
            const tableOverlay = document.getElementById('tableOverlay');
            const liveFilters = document.querySelectorAll('.live-filter');
            const filterForm = document.getElementById('filterForm');
            let timeout = null;

            function fetchResults(url = null) {
                // Hiển thị loading
                searchSpinner.classList.remove('hidden');
                tableOverlay.classList.remove('hidden');

                if (!url) {
                    const formData = new FormData(filterForm);
                    const params = new URLSearchParams(formData).toString();
                    url = "{{ route($routePrefix . 'academic-results.index') }}?" + params;
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
                        searchSpinner.classList.add('hidden');
                        tableOverlay.classList.add('hidden');
                    })
                    .catch(error => {
                        console.error(error);
                        searchSpinner.classList.add('hidden');
                        tableOverlay.classList.add('hidden');
                    });
            }

            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => fetchResults(), 500);
            });

            liveFilters.forEach(select => {
                select.addEventListener('change', () => fetchResults());
            });

            document.addEventListener('click', function(e) {
                if (e.target.closest('.pagination a')) {
                    e.preventDefault();
                    fetchResults(e.target.closest('.pagination a').href);
                }
            });

            document.getElementById('toggleFilterBtn').addEventListener('click', function() {
                document.getElementById('filterPanel').classList.toggle('hidden');
            });

            window.resetFilters = function() {
                searchInput.value = '';
                liveFilters.forEach(el => el.value = '');
                fetchResults();
            }
        });
    </script>
@endsection
