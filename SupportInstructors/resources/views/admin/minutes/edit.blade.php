@extends('layouts.admin')
@section('title', (Auth::user()->role_id ?? 0) == 1 ? 'Kiểm duyệt biên bản' : 'Chỉnh sửa biên bản')

@section('styles')
<style>
    /* Chỉnh giao diện CKEditor cho giống Word */
    .ck-editor__editable_inline {
        min-height: 250px !important;
        font-family: 'Times New Roman', serif;
        font-size: 16px;
        line-height: 1.6;
        padding: 20px !important;
    }
    /* Ẩn thanh trạng thái path dưới cùng */
    .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
        border-color: #e2e8f0;
    }
</style>
@endsection

@section('content')
{{-- Thêm CDN CKEditor --}}
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<form action="{{ route('admin.minutes.update', $minute->id) }}" method="POST" class="h-[calc(100vh-65px)] flex flex-col overflow-hidden">
    @csrf
    @method('PUT')
    <input type="hidden" name="class_id" value="{{ $minute->class_id }}">

    {{-- 1. HEADER CỐ ĐỊNH --}}
    <div class="h-16 bg-white dark:bg-[#1e1e2d] border-b border-slate-200 dark:border-slate-700 flex items-center justify-between px-6 z-20 shrink-0">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.minutes.index') }}" class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-500 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-lg font-bold text-slate-800 dark:text-white uppercase">
                    {{ (Auth::user()->role_id ?? 0) == 1 ? 'Kiểm duyệt nội dung' : 'Chỉnh sửa nội dung' }}
                </h1>
                <p class="text-xs text-slate-500">
                    Lớp: <span class="font-bold text-primary">{{ $minute->studentClass->code ?? '...' }}</span>
                </p>
            </div>
        </div>
        
        <div class="text-xs text-slate-400 italic">
            @if($minute->status == 'published')
                <span class="text-emerald-600 font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[16px]">lock</span> Đã chốt sổ</span>
            @else
                Đang chỉnh sửa...
            @endif
        </div>
    </div>

    {{-- 2. BODY: CHIA 2 CỘT (CUỘN ĐƯỢC) --}}
    <div class="flex flex-1 overflow-hidden">
        
        {{-- CỘT TRÁI: THÔNG TIN CƠ BẢN --}}
        <div class="w-[400px] bg-slate-50 dark:bg-[#151521] border-r border-slate-200 dark:border-slate-700 flex flex-col overflow-y-auto custom-scrollbar">
            <div class="p-5 space-y-6">
                {{-- Group 1: Thông tin chung --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">info</span> Thông tin chung
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Lớp sinh hoạt</label>
                            <input type="text" readonly value="{{ $minute->studentClass->code }} - {{ $minute->studentClass->name }}"
                                class="w-full rounded border-slate-200 bg-slate-100 text-sm text-slate-500 font-bold cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Tiêu đề biên bản <span class="text-red-500">*</span></label>
                            <input type="text" name="title" required class="w-full rounded border-slate-300 text-sm focus:ring-primary font-bold" 
                                value="{{ old('title', $minute->title) }}">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 dark:text-slate-300 mb-1">Học kỳ <span class="text-red-500">*</span></label>
                            <select name="semester_id" class="w-full rounded border-slate-300 text-sm focus:ring-primary">
                                @foreach($semesters as $sem)
                                    <option value="{{ $sem->id }}" {{ $minute->semester_id == $sem->id ? 'selected' : '' }}>
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
                                <input type="datetime-local" name="held_at" required 
                                    value="{{ old('held_at', $minute->held_at ? $minute->held_at->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full rounded border-slate-300 text-xs focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Kết thúc</label>
                                <input type="datetime-local" name="ended_at"
                                    value="{{ old('ended_at', $minute->ended_at ? $minute->ended_at->format('Y-m-d\TH:i') : '') }}"
                                    class="w-full rounded border-slate-300 text-xs focus:ring-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Địa điểm</label>
                            <input type="text" name="location" required class="w-full rounded border-slate-300 text-sm focus:ring-primary" 
                                value="{{ old('location', $minute->location) }}">
                        </div>
                    </div>
                </div>

                {{-- Group 3: Nhân sự --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-lg shadow-sm border border-slate-200 dark:border-slate-700">
                    <h3 class="text-xs font-bold text-slate-400 uppercase mb-3 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[16px]">group</span> Nhân sự chủ chốt
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Cố vấn học tập</label>
                            <input type="text" readonly value="{{ $minute->studentClass->advisor->user->name ?? 'Chưa cập nhật' }}" 
                                class="w-full rounded border-slate-200 bg-slate-100 text-xs text-slate-500 cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-700 mb-1">Chủ trì (Lớp trưởng)</label>
                            <select name="monitor_id" id="select-monitor" autocomplete="off">
                                <option value="">-- Chọn --</option>
                                @foreach($students as $st)
                                    <option value="{{ $st->id }}" {{ $minute->monitor_id == $st->id ? 'selected' : '' }}>
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
                                    <option value="{{ $st->id }}" {{ $minute->secretary_id == $st->id ? 'selected' : '' }}>
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
                        <div class="text-green-600">Có mặt: <strong id="display-present">0</strong></div>
                        <div class="text-red-600">Vắng: <strong id="display-absent">0</strong></div>
                    </div>
                    <div>
                        <select name="absent_list[]" id="select-absent" multiple placeholder="Chọn người vắng..." autocomplete="off">
                            @foreach($students as $st)
                                @php
                                    $isAbsent = in_array($st->id, $minute->absent_list ?? []);
                                @endphp
                                <option value="{{ $st->id }}" {{ $isAbsent ? 'selected' : '' }}>
                                    {{ $st->fullname }} ({{ $st->student_code }})
                                </option>
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
                        <textarea name="content_discussions" id="editor-discussions">{{ old('content_discussions', $minute->content_discussions) }}</textarea>
                        
                        {{-- Quick Actions - Sửa hàm onClick --}}
                        <div class="flex flex-wrap gap-2 mt-4">
                            <button type="button" onclick="insertToEditor('editor-discussions', 'Triển khai kế hoạch học tập học kỳ mới.')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-md transition border border-slate-200">+ Kế hoạch học tập</button>
                            <button type="button" onclick="insertToEditor('editor-discussions', 'Nhắc nhở đóng học phí đúng hạn.')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-md transition border border-slate-200">+ Học phí</button>
                            <button type="button" onclick="insertToEditor('editor-discussions', 'Đánh giá điểm rèn luyện.')" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium rounded-md transition border border-slate-200">+ Điểm rèn luyện</button>
                        </div>
                    </div>
                </div>

                {{-- MỤC III --}}
                <div class="bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase">III. Kết luận</h2>
                    </div>
                    <div class="p-6">
                        <textarea name="content_conclusion" id="editor-conclusion">{{ old('content_conclusion', $minute->content_conclusion) }}</textarea>
                    </div>
                </div>

                {{-- MỤC IV --}}
                <div class="bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="bg-slate-50 dark:bg-slate-800 px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white uppercase">IV. Kiến nghị của sinh viên</h2>
                    </div>
                    <div class="p-6">
                        <textarea name="content_requests" id="editor-requests">{{ old('content_requests', $minute->content_requests) }}</textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. FOOTER CỐ ĐỊNH Ở DƯỚI CÙNG --}}
    <div class="h-16 bg-white dark:bg-[#1e1e2d] border-t border-slate-200 dark:border-slate-700 flex items-center justify-end px-6 gap-3 z-20 shrink-0">
        
        <a href="{{ route('admin.minutes.index') }}" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded font-medium transition">
            Hủy bỏ
        </a>

        {{-- KHU VỰC DUYỆT CỦA ADMIN --}}
        @if((Auth::user()->role_id ?? 0) == 1)
            <button type="submit" 
                    formaction="{{ route('admin.minutes.approve', $minute->id) }}" 
                    class="px-6 py-2 bg-emerald-600 text-white font-bold rounded shadow-lg shadow-emerald-600/30 hover:bg-emerald-700 flex items-center gap-2">
                <span class="material-symbols-outlined !text-[18px]">check_circle</span> Duyệt & Công bố
            </button>
        @endif

        {{-- Nút Lưu lại (Ai cũng thấy) --}}
        <button type="submit" name="action" value="draft" class="px-5 py-2 bg-primary hover:bg-primary/90 text-white font-bold rounded shadow-md shadow-primary/30 flex items-center gap-2 transition transform active:scale-95">
            <span class="material-symbols-outlined !text-[18px]">save</span> Lưu thay đổi
        </button>
    </div>

</form>

{{-- SCRIPTS --}}
<script>
    const editors = {}; // Lưu instance editor để dùng hàm insert

    function initEditor(id) {
        ClassicEditor
            .create(document.querySelector('#' + id), {
                toolbar: [ 'heading', '|', 'bold', 'italic', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo' ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Normal', class: 'ck-heading_paragraph' },
                        { model: 'heading3', view: 'h3', title: 'Tiêu đề', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                editors[id] = editor;
            })
            .catch(error => {
                console.error(error);
            });
    }

    // Hàm chèn nhanh vào CKEditor
    function insertToEditor(editorId, text) {
        if (editors[editorId]) {
            const editor = editors[editorId];
            const viewFragment = editor.data.processor.toView( '<p>' + text + '</p>' );
            const modelFragment = editor.data.toModel( viewFragment );
            editor.model.insertContent( modelFragment );
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo 3 editor
        initEditor('editor-discussions');
        initEditor('editor-conclusion');
        initEditor('editor-requests');

        // Logic TomSelect (Giữ nguyên)
        var commonConfig = { create: false, sortField: { field: "text", direction: "asc" }, render: { no_results: function(data, escape) { return '<div class="no-results p-2 text-sm text-slate-500 italic">Không tìm thấy sinh viên</div>'; } } };
        var tomMonitor = new TomSelect("#select-monitor", commonConfig);
        var tomSecretary = new TomSelect("#select-secretary", commonConfig);
        var selectAbsent = new TomSelect("#select-absent", { ...commonConfig, plugins: ['remove_button'], onItemAdd: updateAbsentCount, onItemRemove: updateAbsentCount });

        updateAbsentCount();

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
            var elPresent = document.getElementById('display-present');
            var elAbsent = document.getElementById('display-absent');
            if(elPresent) elPresent.innerText = totalStudents - totalAbsent;
            if(elAbsent) elAbsent.innerText = totalAbsent;
        }
    });
</script>
@endsection