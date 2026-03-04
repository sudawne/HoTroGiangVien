@extends('layouts.admin')
@section('title', 'Tạo biên bản họp lớp')

@section('styles')
<style>
    /* Chỉnh chiều cao tối thiểu cho khung soạn thảo */
    .ck-editor__editable_inline {
        min-height: 250px !important;
        font-family: 'Times New Roman', serif; /* Font giống Word */
        font-size: 16px;
    }
    /* Ẩn thanh trạng thái path dưới cùng nếu không cần */
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: #e2e8f0;
    }
</style>
@endsection

@section('content')
{{-- Thêm CDN CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<form action="{{ route('admin.minutes.store') }}" method="POST" class="h-[calc(100vh-65px)] flex flex-col overflow-hidden">
    @csrf
    <input type="hidden" name="class_id" value="{{ $currentClass->id ?? '' }}">

    {{-- HEADER --}}
    <div class="h-16 bg-white dark:bg-[#1e1e2d] border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 z-20 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.minutes.index') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 dark:text-white uppercase">Tạo Biên Bản Mới</h1>
                <p class="text-xs text-slate-500">
                    Lớp: <span class="font-bold text-primary">{{ $currentClass->code ?? '...' }}</span>
                </p>
            </div>
        </div>
        
        <div class="text-xs text-slate-400 italic">
            Đang soạn thảo...
        </div>
    </div>

    {{-- BODY: CHIA 2 CỘT --}}
    <div class="flex flex-1 overflow-hidden">
        
        {{-- CỘT TRÁI: THÔNG TIN (Giữ nguyên) --}}
        <div class="w-[400px] bg-slate-50 dark:bg-[#151521] border-r border-slate-200 dark:border-slate-700 flex flex-col overflow-y-auto custom-scrollbar">
            <div class="p-5 space-y-6">
                
                {{-- Group 1: Thông tin chung --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">info</span> Thông tin chung
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Lớp sinh hoạt <span class="text-red-500">*</span></label>
                            <select name="class_id" onchange="window.location.href = '?class_id=' + this.value"
                                class="w-full rounded border-slate-300 bg-blue-50 text-sm focus:ring-primary font-bold text-blue-700">
                                @foreach($classes as $cls)
                                    <option value="{{ $cls->id }}" {{ (isset($currentClass) && $currentClass->id == $cls->id) ? 'selected' : '' }}>
                                        {{ $cls->code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Tiêu đề biên bản <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full rounded border-slate-300 text-sm focus:ring-primary font-bold" 
                                value="Biên bản họp lớp tháng {{ now()->format('m/Y') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Học kỳ <span class="text-red-500">*</span></label>
                            <select name="semester_id" class="w-full rounded border-slate-300 text-sm focus:ring-primary">
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $sem->is_current ? 'selected' : '' }}>
                                        {{ $sem->name }} ({{ $sem->academic_year }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Group 2: Thời gian & Địa điểm --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">schedule</span> Thời gian & Địa điểm
                    </h3>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Bắt đầu</label>
                                <input type="datetime-local" name="held_at" required value="{{ now()->format('Y-m-d\TH:i') }}"
                                    class="w-full rounded border-slate-300 text-xs focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kết thúc</label>
                                <input type="datetime-local" name="ended_at"
                                    class="w-full rounded border-slate-300 text-xs focus:ring-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Địa điểm</label>
                            <input type="text" name="location" required class="w-full rounded border-slate-300 text-sm focus:ring-primary" 
                                placeholder="VD: Phòng B511">
                        </div>
                    </div>
                </div>

                {{-- Group 3: Thành phần --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">group</span> Nhân sự chủ chốt
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Cố vấn học tập</label>
                            <input type="text" readonly value="{{ $currentClass->advisor->user->name ?? 'Chưa cập nhật' }}" 
                                class="w-full rounded border-slate-200 bg-slate-100 text-xs text-slate-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Chủ trì (Lớp trưởng)</label>
                            <select name="monitor_id" id="select-monitor" autocomplete="off">
                                <option value="">-- Chọn --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}" {{ ($currentClass->monitor_id == $st->id) ? 'selected' : '' }}>
                                        {{ $st->fullname }} ({{ $st->student_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Thư ký</label>
                            <select name="secretary_id" id="select-secretary" autocomplete="off">
                                <option value="">-- Chọn --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}">
                                        {{ $st->fullname }} ({{ $st->student_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Group 4: Điểm danh --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">fact_check</span> Điểm danh
                    </h3>
                    <div class="flex items-center justify-between mb-3 text-xs">
                        <div class="text-slate-600">Tổng: <strong>{{ $students->count() }}</strong></div>
                        <div class="text-green-600">Có mặt: <strong id="display-present">{{ $students->count() }}</strong></div>
                        <div class="text-red-600">Vắng: <strong id="display-absent">0</strong></div>
                    </div>
                    <div>
                        <select name="absent_list[]" id="select-absent" multiple placeholder="Chọn người vắng..." autocomplete="off">
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ $st->fullname }} ({{ $st->student_code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- CỘT PHẢI: NỘI DUNG CHÍNH (Đã thay bằng CKEditor) --}}
        <div class="flex-1 bg-slate-100 dark:bg-slate-900 overflow-y-auto custom-scrollbar p-8">
            <div class="max-w-4xl mx-auto space-y-8 pb-20">
                
                {{-- MỤC II --}}
                <div class="bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase">II. Nội dung cuộc họp</h2>
                    </div>
                    <div class="p-6">
                        {{-- Thêm ID editor-content --}}
                        <textarea name="content_discussions" id="editor-content" placeholder="Nhập nội dung..."></textarea>
                        
                        {{-- Quick Actions - Cần JS đặc biệt để chèn vào CKEditor --}}
                        <div class="flex flex-wrap gap-2 mt-4">
                            <button type="button" onclick="insertToEditor('editor-content', 'Triển khai kế hoạch học tập học kỳ mới.')" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-md hover:bg-slate-200 transition">+ Kế hoạch học tập</button>
                            <button type="button" onclick="insertToEditor('editor-content', 'Nhắc nhở đóng học phí đúng hạn.')" class="px-3 py-1.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-md hover:bg-slate-200 transition">+ Học phí</button>
                        </div>
                    </div>
                </div>

                {{-- MỤC III --}}
                <div class="bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase">III. Kết luận</h2>
                    </div>
                    <div class="p-6">
                        {{-- Thêm ID editor-conclusion --}}
                        <textarea name="content_conclusion" id="editor-conclusion" placeholder="Kết quả thống nhất..."></textarea>
                    </div>
                </div>

                {{-- MỤC IV --}}
                <div class="bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase">IV. Kiến nghị của sinh viên</h2>
                    </div>
                    <div class="p-6">
                        {{-- Thêm ID editor-requests --}}
                        <textarea name="content_requests" id="editor-requests" placeholder="Ý kiến sinh viên..."></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="h-16 bg-white dark:bg-[#1e1e2d] border-t border-slate-200 dark:border-slate-700 flex items-center justify-end px-6 gap-3 z-20 shrink-0">
        <button type="button" onclick="window.history.back()" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded font-medium transition">
            Hủy bỏ
        </button>
        <button type="submit" name="action" value="draft" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold rounded shadow-sm transition">
            Lưu nháp
        </button>
        <button type="submit" name="action" value="publish" class="px-6 py-2 bg-primary hover:bg-primary/90 text-white font-bold rounded shadow-md shadow-primary/30 flex items-center gap-2 transition transform active:scale-95">
            <span class="material-symbols-outlined !text-[18px]">save_as</span> Hoàn tất
        </button>
    </div>

</form>

{{-- SCRIPTS --}}
<script>
    // Lưu các instance CKEditor vào object để dùng lại (ví dụ chèn nội dung nhanh)
    const editors = {};

    // Hàm khởi tạo CKEditor cho 1 ID
    function initEditor(id) {
        ClassicEditor
            .create(document.querySelector('#' + id), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                        { model: 'heading1', view: 'h3', title: 'Tiêu đề nhỏ', class: 'ck-heading_heading1' }
                    ]
                }
            })
            .then(editor => {
                editors[id] = editor; // Lưu instance
            })
            .catch(error => {
                console.error(error);
            });
    }

    // Khởi tạo cho 3 ô
    document.addEventListener('DOMContentLoaded', function() {
        initEditor('editor-content');
        initEditor('editor-conclusion');
        initEditor('editor-requests');

        // Logic TomSelect (Giữ nguyên)
        var commonConfig = { create: false, sortField: { field: "text", direction: "asc" } };
        var tomMonitor = new TomSelect("#select-monitor", commonConfig);
        var tomSecretary = new TomSelect("#select-secretary", commonConfig);
        var selectAbsent = new TomSelect("#select-absent", {
            ...commonConfig,
            plugins: ['remove_button'],
            onItemAdd: updateAbsentCount,
            onItemRemove: updateAbsentCount
        });

        tomMonitor.on('change', function(value) {
            if (value && value === tomSecretary.getValue()) {
                alert('Lớp trưởng và Thư ký không được là cùng một người!');
                tomMonitor.clear(); 
            }
        });
        tomSecretary.on('change', function(value) {
            if (value && value === tomMonitor.getValue()) {
                alert('Lớp trưởng và Thư ký không được là cùng một người!');
                tomSecretary.clear();
            }
        });

        function updateAbsentCount() {
            var totalAbsent = selectAbsent.items.length;
            var totalStudents = {{ $students->count() }};
            document.getElementById('display-present').innerText = totalStudents - totalAbsent;
            document.getElementById('display-absent').innerText = totalAbsent;
        }
    });

    // Hàm chèn nội dung nhanh vào CKEditor
    function insertToEditor(editorId, text) {
        if (editors[editorId]) {
            const editor = editors[editorId];
            const viewFragment = editor.data.processor.toView( '<p>' + text + '</p>' );
            const modelFragment = editor.data.toModel( viewFragment );
            editor.model.insertContent( modelFragment );
        }
    }
</script>
@endsection