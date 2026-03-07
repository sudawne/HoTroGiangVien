@extends('layouts.admin')

@section('title', 'Cảnh báo học tập')

@section('content')
    <div class="max-w-[1400px] mx-auto" x-data="{ showExportModal: false }">

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
                <button @click="showExportModal = true" 
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Báo cáo
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
            {{-- Form này không cần action submit vì ta dùng JS, nhưng để method GET để giữ URL clean --}}
            <form id="filterForm" method="GET">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
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
                        
                        {{-- Nút Excel giữ nguyên --}}
                        <a href="{{ route('admin.academic_warnings.import') }}" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                            <span class="hidden sm:inline">Nhập Excel</span>
                        </a>
                    </div>
                </div>

                {{-- [SỬA] Khu vực Lọc nâng cao: Thêm class 'live-filter' để JS bắt sự kiện change --}}
                <div id="filterPanel" class="{{ request()->hasAny(['semester_id', 'level', 'class_id']) ? '' : 'hidden' }} mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                            <select name="semester_id" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả học kỳ</option>
                                @foreach($semesters->groupBy('academic_year') as $year => $groupSems)
                                    <optgroup label="Năm học {{ $year }}">
                                        @foreach($groupSems as $sem)
                                            <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                                {{ $sem->name }}
                                                @if($sem->is_current) (Hiện tại) @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Mức độ</label>
                            <select name="level" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả mức độ</option>
                                <option value="1" {{ request('level') == '1' ? 'selected' : '' }}>Mức 1</option>
                                <option value="2" {{ request('level') == '2' ? 'selected' : '' }}>Mức 2</option>
                                <option value="3" {{ request('level') == '3' ? 'selected' : '' }}>Buộc thôi học</option>
                            </select>
                        </div>
                        <div class="relative group">
                            <label class="block text-xs font-medium text-slate-500 mb-1">Lớp</label>
                            <select name="class_id" class="live-filter w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8">
                                <option value="">Tất cả lớp</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>{{ $class->code }}</option>
                                @endforeach
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
        <div class="bg-white dark:bg-[#1e1e2d] rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative">
            {{-- Lớp phủ Loading (Mờ đi khi đang search) --}}
            <div id="tableOverlay" class="absolute inset-0 bg-white/50 dark:bg-black/20 z-10 hidden"></div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">MSSV</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Họ và tên</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lớp</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Học kỳ</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">GPA</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-center">Nợ tín</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Mức cảnh báo</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase">Lý do</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-500 uppercase text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                        @include('admin.academic_warnings.partials.table_rows')
                    </tbody>
                </table>
            </div>

            {{-- Phân trang (Cần bọc ID để update luôn nếu muốn) --}}
            <div id="paginationContainer" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
                {{ $warnings->links() }}
            </div>
        </div>

        <div x-show="showExportModal" 
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            
            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-xl w-full max-w-lg overflow-hidden" 
                 @click.away="showExportModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                
                {{-- Header Modal --}}
                <div class="bg-slate-50 dark:bg-slate-800/50 px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-blue-600">print_connect</span>
                        Tùy chọn Xuất báo cáo
                    </h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-red-500 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Form Xuất Báo Cáo --}}
                {{-- Lưu ý: Bạn cần tạo route admin.academic_warnings.export trong web.php --}}
                <form action="{{ route('admin.academic_warnings.export') }}" method="GET" class="p-6">
                    
                    {{-- 1. Các tiêu chí lọc --}}
                    <div class="space-y-4 mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">1. Phạm vi dữ liệu</p>
                        
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Chọn Học kỳ --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Học kỳ</label>
                                <select name="semester_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    @foreach($semesters as $sem)
                                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                                            {{ $sem->name }} ({{ $sem->academic_year }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Chọn Mức cảnh báo --}}
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Mức cảnh báo</label>
                                <select name="level" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Tất cả --</option>
                                    <option value="1">Mức 1</option>
                                    <option value="2">Mức 2</option>
                                    <option value="3">Buộc thôi học</option>
                                </select>
                            </div>
                        </div>

                        {{-- Chọn Lớp --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Lớp sinh hoạt</label>
                            <select name="class_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                <option value="">-- Tất cả các lớp --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->code }} - {{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- 2. Chọn định dạng File --}}
                    <div class="mb-6">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">2. Định dạng file</p>
                        <div class="grid grid-cols-2 gap-4">
                            {{-- Option Excel --}}
                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="excel" class="peer sr-only" checked>
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-green-500 peer-checked:bg-green-50/50 dark:peer-checked:bg-green-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-green-600 peer-checked:text-green-600 transition-colors">table_view</span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-green-700">Xuất Excel</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-green-500 peer-checked:border-green-500 transition-colors"></div>
                            </label>

                            {{-- Option PDF --}}
                            <label class="cursor-pointer relative">
                                <input type="radio" name="format" value="pdf" class="peer sr-only">
                                <div class="p-4 rounded-lg border-2 border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 peer-checked:border-red-500 peer-checked:bg-red-50/50 dark:peer-checked:bg-red-900/10 transition-all flex flex-col items-center gap-2 text-center group">
                                    <span class="material-symbols-outlined text-3xl text-slate-400 group-hover:text-red-600 peer-checked:text-red-600 transition-colors">picture_as_pdf</span>
                                    <span class="text-sm font-bold text-slate-600 dark:text-slate-300 peer-checked:text-red-700">Xuất PDF</span>
                                </div>
                                <div class="absolute top-2 right-2 w-4 h-4 rounded-full border border-slate-300 bg-white peer-checked:bg-red-500 peer-checked:border-red-500 transition-colors"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Footer Buttons --}}
                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showExportModal = false" class="px-4 py-2 bg-white border border-slate-300 rounded-sm text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-sm text-sm font-bold hover:bg-blue-700 shadow-lg shadow-blue-600/20 flex items-center gap-2 transition-all transform active:scale-95">
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

            // Biến debounce để tránh gọi server liên tục khi gõ nhanh
            let timeout = null;

            // Hàm gọi Ajax
            function fetchResults() {
                // Hiện loading
                searchSpinner.classList.remove('hidden');
                tableOverlay.classList.remove('hidden');

                // Lấy dữ liệu từ Form
                const formData = new FormData(document.getElementById('filterForm'));
                const params = new URLSearchParams(formData).toString();

                // Cập nhật URL trên browser (để F5 không mất kết quả lọc)
                const newUrl = `${window.location.pathname}?${params}`;
                window.history.pushState({path: newUrl}, '', newUrl);

                fetch(newUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Đánh dấu là Ajax để Controller biết
                    }
                })
                .then(response => response.text())
                .then(html => {
                    tableBody.innerHTML = html; // Cập nhật nội dung bảng
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                })
                .catch(error => {
                    console.error('Lỗi search:', error);
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                });
            }

            // 1. Sự kiện khi gõ phím vào ô Search (Debounce 500ms)
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(fetchResults, 500);
            });

            // 2. Sự kiện khi thay đổi các Select (Học kỳ, Mức độ, Lớp)
            liveFilters.forEach(select => {
                select.addEventListener('change', fetchResults);
            });

            // Hàm Reset bộ lọc
            window.resetFilters = function() {
                searchInput.value = '';
                liveFilters.forEach(el => el.value = '');
                fetchResults();
            }

            // Script đóng mở bộ lọc cũ (Giữ nguyên)
            const toggleBtn = document.getElementById('toggleFilterBtn');
            const filterPanel = document.getElementById('filterPanel');
            toggleBtn.addEventListener('click', function() {
                filterPanel.classList.toggle('hidden');
            });
        });
    </script>
@endsection