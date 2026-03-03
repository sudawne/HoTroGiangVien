@extends('layouts.admin')
@section('title', 'Tạo biên bản họp lớp')

@section('content')
<form action="{{ route('admin.minutes.store') }}" method="POST" class="max-w-5xl mx-auto pb-20">
    @csrf
    
    {{-- Input ẩn để lưu ID lớp --}}
    <input type="hidden" name="class_id" value="{{ $currentClass->id ?? '' }}">

    {{-- HEADER --}}
    <div class="flex items-center justify-between gap-4 mb-6 sticky top-4 z-20 bg-slate-50/95 dark:bg-[#151521]/95 backdrop-blur py-2">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.minutes.index') }}" class="p-2 rounded-full bg-white border hover:bg-slate-50 text-slate-500 shadow-sm">
                <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Tạo Biên Bản Họp Lớp</h1>
                <p class="text-slate-500 text-sm">
                    Lớp: <strong class="text-primary">{{ $currentClass->code ?? 'N/A' }}</strong> - {{ $currentClass->name ?? '' }}
                </p>
            </div>
        </div>
        <div class="flex gap-2">
            <button type="submit" name="action" value="draft" class="px-4 py-2 bg-white border border-slate-300 text-slate-700 font-bold rounded shadow-sm hover:bg-slate-50">
                Lưu nháp
            </button>
            <button type="submit" name="action" value="publish" class="px-6 py-2 bg-primary text-white font-bold rounded shadow-lg shadow-primary/30 hover:bg-primary/90 flex items-center gap-2">
                <span class="material-symbols-outlined !text-[18px]">save</span> Lưu & Xuất Word
            </button>
        </div>
    </div>

    {{-- PHẦN 0: THÔNG TIN CƠ BẢN --}}
    <div class="bg-white dark:bg-[#1e1e2d] p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6"> 
            
            {{-- Chọn Lớp học --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Lớp sinh hoạt <span class="text-red-500">*</span></label>
                <select name="class_id" 
                    onchange="window.location.href = '?class_id=' + this.value"
                    class="w-full rounded border-slate-300 bg-blue-50 focus:ring-primary font-bold text-blue-700">
                    @foreach($classes as $cls)
                        <option value="{{ $cls->id }}" {{ (isset($currentClass) && $currentClass->id == $cls->id) ? 'selected' : '' }}>
                            {{ $cls->code }} - {{ $cls->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1 italic">* Chọn lớp để cập nhật danh sách SV</p>
            </div>

            {{-- Tên biên bản --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Tên biên bản / Về việc <span class="text-red-500">*</span></label>
                <input type="text" name="title" required class="w-full rounded border-slate-300 focus:ring-primary font-bold" 
                    placeholder="VD: Về việc..." value="Biên bản họp lớp tháng {{ now()->format('m/Y') }}">
            </div>

            {{-- [CẬP NHẬT] Chọn Học kỳ hiển thị Năm học --}}
            <div>
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Học kỳ & Năm học</label>
                <select name="semester_id" class="w-full rounded border-slate-300 bg-slate-50 focus:ring-primary">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ $sem->is_current ? 'selected' : '' }}>
                            {{-- Hiển thị: Học kỳ 1 (2025-2026) --}}
                            {{ $sem->name }} ({{ $sem->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- MỤC I --}}
    <div class="bg-white dark:bg-[#1e1e2d] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6 overflow-hidden">
        <div class="bg-slate-100 dark:bg-slate-800 px-6 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white uppercase">I. Thời gian, Địa điểm, Thành phần</h3>
        </div>
        
        <div class="p-6 space-y-6">
            {{-- Thời gian & Địa điểm --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">1. Thời gian bắt đầu</label>
                    <input type="datetime-local" name="held_at" required 
                           value="{{ now()->format('Y-m-d\TH:i') }}"
                           class="w-full rounded border-slate-300 focus:ring-primary">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">2. Địa điểm</label>
                    <input type="text" name="location" required class="w-full rounded border-slate-300 focus:ring-primary" 
                        placeholder="VD: Phòng B511">
                </div>
            </div>

            <hr class="border-slate-100 dark:border-slate-700">

            {{-- Thành phần --}}
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase mb-3">3. Thành phần tham dự</label>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Cố vấn --}}
                    <div>
                        <span class="text-sm text-slate-500 block mb-1">Cố vấn học tập</span>
                        <input type="text" readonly 
                            value="{{ $currentClass->advisor->user->name ?? 'Chưa cập nhật' }}" 
                            class="w-full rounded border-slate-200 bg-slate-100 text-slate-500 cursor-not-allowed">
                    </div>
                    
                    {{-- [CẬP NHẬT] Chủ trì (Thêm ID để xử lý JS) --}}
                    <div>
                        <span class="text-sm text-slate-500 block mb-1">Chủ trì (Lớp trưởng)</span>
                        <select name="monitor_id" id="monitor_select" class="w-full rounded border-slate-300 focus:ring-primary">
                            <option value="">-- Chọn sinh viên --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" {{ ($currentClass->monitor_id == $st->id) ? 'selected' : '' }}>
                                    {{ $st->fullname }} ({{ $st->student_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- [CẬP NHẬT] Thư ký (Thêm ID để xử lý JS) --}}
                    <div>
                        <span class="text-sm text-slate-500 block mb-1">Thư ký</span>
                        <select name="secretary_id" id="secretary_select" class="w-full rounded border-slate-300 focus:ring-primary">
                            <option value="">-- Chọn sinh viên --</option>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">
                                    {{ $st->fullname }} ({{ $st->student_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Điểm danh --}}
            <div class="bg-blue-50 dark:bg-blue-900/10 p-4 rounded-lg border border-blue-100 dark:border-blue-800"
                x-data="{ total: {{ $students->count() }}, absent_count: 0 }">
                <div class="flex items-center gap-8 mb-3">
                    <div class="text-sm">Tổng số: <strong x-text="total"></strong></div>
                    <div class="text-sm text-green-600">Có mặt: <strong x-text="total - absent_count"></strong></div>
                    <div class="text-sm text-red-600">Vắng: <strong x-text="absent_count"></strong></div>
                </div>
                
                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Danh sách vắng (Chọn nhiều)</label>
                    <select name="absent_list[]" multiple size="5"
                        x-on:change="absent_count = $event.target.selectedOptions.length"
                        class="w-full rounded border-slate-300 text-sm bg-white focus:ring-primary">
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->fullname }} ({{ $st->student_code }})</option>
                        @endforeach
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">* Giữ phím Ctrl để chọn nhiều</p>
                </div>
            </div>
        </div>
    </div>

    {{-- MỤC II: NỘI DUNG --}}
    <div class="bg-white dark:bg-[#1e1e2d] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
        <div class="bg-slate-100 dark:bg-slate-800 px-6 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white uppercase">II. Nội dung</h3>
        </div>
        <div class="p-6">
            <textarea name="content_discussions" rows="8" class="w-full rounded border-slate-300 focus:ring-primary" placeholder="Nhập nội dung triển khai..."></textarea>
            
            {{-- Gợi ý nội dung nhanh --}}
            <div class="flex gap-2 mt-2">
                <button type="button" onclick="appendContent('Triển khai kế hoạch học tập học kỳ mới.')" class="px-3 py-1 bg-slate-100 text-slate-600 text-xs rounded-full hover:bg-slate-200 transition">+ Kế hoạch học tập</button>
                <button type="button" onclick="appendContent('Nhắc nhở sinh viên hoàn thành đóng học phí đúng hạn.')" class="px-3 py-1 bg-slate-100 text-slate-600 text-xs rounded-full hover:bg-slate-200 transition">+ Đóng học phí</button>
                <button type="button" onclick="appendContent('Triển khai vấn đề điểm rèn luyên và thời gian đánh giá điểm rèn luyên.')" class="px-3 py-1 bg-slate-100 text-slate-600 text-xs rounded-full hover:bg-slate-200 transition">+ Điểm rèn luyện</button>
            </div>
        </div>
    </div>

    {{-- MỤC III: KẾT LUẬN --}}
    <div class="bg-white dark:bg-[#1e1e2d] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
        <div class="bg-slate-100 dark:bg-slate-800 px-6 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white uppercase">III. Kết luận</h3>
        </div>
        <div class="p-6">
            <textarea name="content_conclusion" rows="5" class="w-full rounded border-slate-300 focus:ring-primary" placeholder="Kết quả thống nhất..."></textarea>
        </div>
    </div>

    {{-- MỤC IV: KIẾN NGHỊ --}}
    <div class="bg-white dark:bg-[#1e1e2d] rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm mb-6">
        <div class="bg-slate-100 dark:bg-slate-800 px-6 py-3 border-b border-slate-200 dark:border-slate-700">
            <h3 class="font-bold text-slate-800 dark:text-white uppercase">IV. Kiến nghị</h3>
        </div>
        <div class="p-6">
            <textarea name="content_requests" rows="4" class="w-full rounded border-slate-300 focus:ring-primary" placeholder="Ý kiến sinh viên..."></textarea>
        </div>
    </div>

    {{-- THỜI GIAN KẾT THÚC --}}
    <div class="bg-white dark:bg-[#1e1e2d] p-6 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm">
        <div class="flex items-center gap-4">
            <label class="text-sm font-bold text-slate-700 uppercase whitespace-nowrap">Kết thúc lúc:</label>
            <input type="datetime-local" name="ended_at" class="w-64 rounded border-slate-300 focus:ring-primary">
        </div>
    </div>

</form>

<script>
    function appendContent(text) {
        const textarea = document.querySelector('textarea[name="content_discussions"]');
        textarea.value += (textarea.value ? '\n' : '') + '- ' + text;
    }

    // [CẬP NHẬT] Script xử lý không chọn trùng Chủ trì và Thư ký
    document.addEventListener('DOMContentLoaded', function () {
        const monitorSelect = document.getElementById('monitor_select');
        const secretarySelect = document.getElementById('secretary_select');

        function preventDuplicateRoles(changedSelect) {
            const monitorVal = monitorSelect.value;
            const secretaryVal = secretarySelect.value;

            // Nếu người được chọn làm Chủ trì đang được chọn làm Thư ký -> Reset Thư ký
            if (changedSelect === monitorSelect && monitorVal === secretaryVal && monitorVal !== "") {
                secretarySelect.value = "";
            }
            // Nếu người được chọn làm Thư ký đang được chọn làm Chủ trì -> Reset Chủ trì
            if (changedSelect === secretarySelect && secretaryVal === monitorVal && secretaryVal !== "") {
                monitorSelect.value = "";
            }

            // --- Xử lý Ẩn/Hiện Visual (UX) ---
            
            // 1. Reset: Bật lại tất cả tùy chọn trước
            Array.from(monitorSelect.options).forEach(opt => opt.disabled = false);
            Array.from(secretarySelect.options).forEach(opt => opt.disabled = false);

            // 2. Disable người đang làm Chủ trì bên phía Thư ký
            if (monitorSelect.value) {
                const opt = secretarySelect.querySelector(`option[value="${monitorSelect.value}"]`);
                if (opt) opt.disabled = true;
            }

            // 3. Disable người đang làm Thư ký bên phía Chủ trì
            if (secretarySelect.value) {
                const opt = monitorSelect.querySelector(`option[value="${secretarySelect.value}"]`);
                if (opt) opt.disabled = true;
            }
        }

        // Gán sự kiện
        monitorSelect.addEventListener('change', () => preventDuplicateRoles(monitorSelect));
        secretarySelect.addEventListener('change', () => preventDuplicateRoles(secretarySelect));

        // Chạy 1 lần lúc mới tải trang để lọc dữ liệu ban đầu
        preventDuplicateRoles(null);
    });
</script>
@endsection