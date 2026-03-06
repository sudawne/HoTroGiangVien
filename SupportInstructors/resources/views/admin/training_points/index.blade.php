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
                <button class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[16px]">send</span>
                    Gửi thông báo hàng loạt
                </button>
                <button class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
            {{-- Card 1: Tổng sinh viên --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Tổng sinh viên</p>
                    {{-- Icon Group màu tím than/indigo --}}
                    <span class="bg-indigo-50 text-indigo-600 p-1 rounded-sm border border-indigo-100">
                        <span class="material-symbols-outlined !text-[16px]">group</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</h2>
            </div>

            {{-- Card 2: Xuất sắc --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Xuất sắc (90+)</p>
                    {{-- Icon Huân chương màu xanh lá --}}
                    <span class="bg-emerald-50 text-emerald-600 p-1 rounded-sm border border-emerald-100">
                        <span class="material-symbols-outlined !text-[16px]">military_tech</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-emerald-600">{{ $stats['xuatsac'] }}</h2>
            </div>

            {{-- Card 3: Tốt --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Tốt (80-89)</p>
                    {{-- Icon Like màu xanh dương --}}
                    <span class="bg-blue-50 text-blue-600 p-1 rounded-sm border border-blue-100">
                        <span class="material-symbols-outlined !text-[16px]">thumb_up</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-blue-600">{{ $stats['tot'] }}</h2>
            </div>

            {{-- Card 4: Khá --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Khá (65-79)</p>
                    {{-- Icon Mặt cười màu xanh trời --}}
                    <span class="bg-sky-50 text-sky-600 p-1 rounded-sm border border-sky-100">
                        <span class="material-symbols-outlined !text-[16px]">sentiment_satisfied</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-sky-500">{{ $stats['kha'] }}</h2>
            </div>

            {{-- Card 5: Yếu/Chưa xét --}}
            <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-24 shadow-sm">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 text-xs font-bold uppercase">Yếu / Chưa xét</p>
                    {{-- Icon Cảnh báo màu đỏ --}}
                    <span class="bg-red-50 text-red-600 p-1 rounded-sm border border-red-100">
                        <span class="material-symbols-outlined !text-[16px]">warning</span>
                    </span>
                </div>
                <h2 class="text-3xl font-bold text-red-500">{{ $stats['yeu'] + $stats['chuaxet'] }}</h2>
            </div>
        </div>

        {{-- BỘ LỌC --}}
        {{-- BỘ LỌC & CÔNG CỤ (Giao diện Warning) --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">
            {{-- Form này không cần action submit vì ta dùng JS Live Search --}}
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    {{-- Ô tìm kiếm --}}
                    <div class="relative flex-1 max-w-md">
                        <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                        <input id="searchInput" name="search" value="{{ request('search') }}"
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                            placeholder="Tìm nhanh tên hoặc MSSV..." type="text" autocomplete="off" />
                        <span id="searchSpinner" class="material-symbols-outlined absolute right-3 top-2.5 text-primary !text-[16px] animate-spin hidden">sync</span>
                    </div>

                    {{-- Nhóm nút thao tác --}}
                    <div class="flex flex-wrap items-center gap-2">
                        <button type="button" id="toggleFilterBtn" class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">filter_list</span> Bộ lọc
                        </button>
                        
                        {{-- Nút Import Excel (Đã sửa route) --}}
                        <a href="{{ route('admin.training_points.import') }}" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>

                {{-- Khu vực Lọc nâng cao --}}
                <div id="filterPanel" class="{{ request()->hasAny(['semester_id', 'rank', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        
                        {{-- 1. Lọc Học kỳ --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả học kỳ</option>
                                {{-- Nếu Controller đã nhóm theo năm, dùng vòng lặp này --}}
                                @if($semesters instanceof \Illuminate\Database\Eloquent\Collection) 
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->name }} ({{ $sem->academic_year }})
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback nếu data dạng khác --}}
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->name }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        {{-- 2. Lọc Xếp loại (Thay thế cho Mức độ) --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Xếp loại</label>
                            <select name="rank" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả xếp loại</option>
                                <option value="xuatsac" {{ request('rank') == 'xuatsac' ? 'selected' : '' }}>Xuất sắc (90-100)</option>
                                <option value="tot" {{ request('rank') == 'tot' ? 'selected' : '' }}>Tốt (80-89)</option>
                                <option value="kha" {{ request('rank') == 'kha' ? 'selected' : '' }}>Khá (65-79)</option>
                                <option value="trungbinh" {{ request('rank') == 'trungbinh' ? 'selected' : '' }}>Trung bình (50-64)</option>
                                <option value="yeu" {{ request('rank') == 'yeu' ? 'selected' : '' }}>Yếu/Kém (<50)</option>
                            </select>
                        </div>

                        {{-- 3. Lọc Lớp --}}
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp</label>
                            <select name="class_id" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả lớp</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
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
        <div class="overflow-x-auto relative min-h-[300px] bg-white">
            {{-- Loading Overlay --}}
            <div id="tableOverlay" class="absolute inset-0 bg-white/50 dark:bg-slate-900/50 z-10 hidden transition-opacity flex items-center justify-center">
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
                {{-- ID "tableBody" dùng để JS nhắm mục tiêu --}}
                <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800 text-sm">
                    @include('admin.training_points.partials.table_rows')
                </tbody>
            </table>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.querySelector('input[name="search"]');
        const filterForm = document.getElementById('filterForm');
        const tableBody = document.getElementById('tableBody');
        const tableOverlay = document.getElementById('tableOverlay');
        const liveFilters = document.querySelectorAll('.live-filter');
        let timeout = null;

        // Hàm gọi AJAX
        function fetchResults(url) {
            // Hiện loading
            tableOverlay.classList.remove('hidden');

            // Nếu không truyền URL thì lấy URL hiện tại + params từ form
            if (!url) {
                const formData = new FormData(filterForm);
                const params = new URLSearchParams(formData).toString();
                url = "{{ route('admin.training_points.index') }}?" + params;
            }

            // Update URL trên trình duyệt để F5 không mất kết quả lọc
            window.history.pushState(null, '', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Bắt buộc để Controller nhận biết là AJAX
                }
            })
            .then(response => response.text())
            .then(html => {
                tableBody.innerHTML = html; // Thay thế nội dung bảng
                tableOverlay.classList.add('hidden'); // Ẩn loading
            })
            .catch(error => {
                console.error('Lỗi:', error);
                tableOverlay.classList.add('hidden');
            });
        }

        // 1. Sự kiện gõ phím (Debounce 500ms)
        searchInput.addEventListener('input', function() {
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                fetchResults();
            }, 500);
        });

        liveFilters.forEach(select => {
            select.addEventListener('change', function() {
                fetchResults(); // Gọi hàm lọc ngay lập tức khi chọn xong
            });
        });

        // 2. Sự kiện khi click vào link phân trang (để chuyển trang không reload)
        document.addEventListener('click', function(e) {
            // Kiểm tra nếu click vào thẻ a trong pagination
            if (e.target.closest('.pagination a')) {
                e.preventDefault();
                const url = e.target.closest('.pagination a').href;
                fetchResults(url);
            }
        });

        // 3. Sự kiện toggle filter (Giữ nguyên của bạn)
        document.getElementById('toggleFilterBtn').addEventListener('click', function() {
            document.getElementById('filterPanel').classList.toggle('hidden');
        });
        window.resetFilters = function() {
            searchInput.value = '';
            liveFilters.forEach(el => el.value = ''); // Reset các select về mặc định
            fetchResults();
        }
    });
</script>
@endsection