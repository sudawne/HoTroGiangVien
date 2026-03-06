@extends('layouts.admin')
@section('title', 'Soạn Thông báo mới')

@section('styles')
    <style>
        .ck-editor__editable_inline {
            min-height: 400px !important;
            font-family: 'Times New Roman', serif;
            font-size: 16px;
        }

        .ck.ck-editor__main>.ck-editor__editable:not(.ck-focused) {
            border-color: #e2e8f0;
        }
    </style>
@endsection

@section('content')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

    <div class="w-full px-4 md:px-8 py-6">
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('admin.notifications.index') }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 py-1.5 px-3 rounded-lg transition-colors -ml-3">
                <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
                Quay lại danh sách
            </a>
            <h1 class="text-xl font-bold text-slate-800 uppercase flex items-center gap-2">
                <span class="material-symbols-outlined text-primary !text-[24px]">campaign</span> Soạn Bài Đăng Mới
            </h1>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
            <form id="notification-form" action="{{ route('admin.notifications.store') }}" method="POST"
                enctype="multipart/form-data" class="p-6 md:p-8 space-y-8">
                @csrf
                <input type="hidden" name="action" id="form-action" value="draft">

                {{-- CHỌN ĐỐI TƯỢNG NHẬN --}}
                <div class="p-5 border border-blue-100 bg-blue-50/30 rounded-lg">
                    <label class="block text-sm font-bold text-slate-800 mb-4">Phạm vi gửi thông báo <span
                            class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-8 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_audience" value="all"
                                class="w-4 h-4 text-primary focus:ring-primary"
                                {{ old('target_audience', 'all') == 'all' ? 'checked' : '' }}
                                onclick="document.getElementById('classSelectBox').classList.add('hidden')">
                            <span class="font-medium text-slate-700">Tất cả sinh viên Toàn Khoa / Toàn trường</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_audience" value="class"
                                class="w-4 h-4 text-primary focus:ring-primary"
                                {{ old('target_audience') == 'class' ? 'checked' : '' }}
                                onclick="document.getElementById('classSelectBox').classList.remove('hidden')">
                            <span class="font-medium text-slate-700">Chỉ gửi cho các Lớp cụ thể</span>
                        </label>
                    </div>

                    {{-- Chọn nhiều lớp bằng TomSelect --}}
                    <div id="classSelectBox"
                        class="{{ old('target_audience') == 'class' ? '' : 'hidden' }} transition-all duration-300 pl-6">
                        <select name="class_ids[]" id="select-multiple-classes" multiple
                            placeholder="Click để chọn một hoặc nhiều lớp..." autocomplete="off" class="w-full text-sm">
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}"
                                    {{ is_array(old('class_ids')) && in_array($c->id, old('class_ids')) ? 'selected' : '' }}>
                                    {{ $c->code }} - {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('class_ids')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- TIÊU ĐỀ --}}
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Tiêu đề bài viết <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                        placeholder="Nhập tiêu đề thật rõ ràng..."
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary text-base font-semibold shadow-sm transition-all">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- MỨC ĐỘ & FILE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Mức độ ưu tiên</label>
                        <select name="type"
                            class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary shadow-sm transition-all">
                            <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>🔵 Thông tin chung (Bình
                                thường)</option>
                            <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>🟡 Cảnh báo / Nhắc nhở
                            </option>
                            <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>🔴 Khẩn cấp</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Đính kèm tài liệu (nếu có)</label>
                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:border-0 file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-300 rounded-lg shadow-sm">
                        @error('attachment')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer w-max">
                            <input type="checkbox" name="allow_comments" value="1" checked
                                class="w-4 h-4 text-primary focus:ring-primary rounded border-slate-300">
                            <span class="font-medium text-slate-700 text-sm">Cho phép mọi người bình luận dưới bài viết
                                này</span>
                        </label>
                    </div>
                </div>

                {{-- NỘI DUNG CKEDITOR --}}
                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Nội dung chi tiết <span
                            class="text-red-500">*</span></label>
                    <div class="rounded-lg shadow-sm border border-slate-200">
                        <textarea id="editor" name="message" placeholder="Bắt đầu soạn thảo nội dung tại đây...">{{ old('message') }}</textarea>
                    </div>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                {{-- NÚT XỬ LÝ --}}
                <div class="pt-6 border-t border-slate-200 flex flex-wrap justify-end gap-3">
                    <button type="button" onclick="submitForm('draft')"
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-lg font-bold hover:bg-slate-300 transition-colors shadow-sm">
                        Lưu Nháp (Chưa gửi)
                    </button>
                    <button type="button" onclick="submitForm('send')"
                        class="px-6 py-3 bg-primary text-white rounded-lg font-bold hover:bg-primary-dark flex items-center gap-2 shadow-md transition-all hover:-translate-y-0.5">
                        <span class="material-symbols-outlined !text-[20px]">send</span>
                        {{ Auth::user()->role_id == 1 ? 'Đăng Bài & Gửi Email Ngay' : 'Gửi Admin Xét Duyệt' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let myEditor;

        document.addEventListener('DOMContentLoaded', function() {
            // 1. Khởi tạo TomSelect (Chọn nhiều lớp)
            if (document.querySelector('#select-multiple-classes')) {
                new TomSelect("#select-multiple-classes", {
                    plugins: ['remove_button'],
                    maxItems: null,
                    placeholder: "Click để chọn một hoặc nhiều lớp...",
                    hideSelected: true
                });
            }

            // 2. Khởi tạo CKEditor 
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                        'blockQuote', '|', 'undo', 'redo'
                    ],
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Đoạn văn bình thường',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h2',
                                title: 'Tiêu đề lớn',
                                class: 'ck-heading_heading1'
                            },
                            {
                                model: 'heading2',
                                view: 'h3',
                                title: 'Tiêu đề nhỏ',
                                class: 'ck-heading_heading2'
                            }
                        ]
                    }
                })
                .then(editor => {
                    myEditor = editor;
                })
                .catch(error => {
                    console.error('Lỗi khởi tạo CKEditor:', error);
                });
        });

        function submitForm(actionType) {
            let form = document.getElementById('notification-form');
            let actionInput = document.getElementById('form-action');

            // Cập nhật dữ liệu từ Editor
            if (myEditor) {
                document.querySelector('#editor').value = myEditor.getData();
            }

            // BẮT LỖI TÙY CHỈNH: Bắt buộc chọn lớp nếu check Radio "Gửi cho Lớp cụ thể"
            let isClassTarget = document.querySelector('input[name="target_audience"][value="class"]').checked;
            let classSelect = document.getElementById('select-multiple-classes');

            if (isClassTarget && classSelect.selectedOptions.length === 0) {
                alert('Vui lòng chọn ít nhất 1 lớp học để gửi thông báo!');
                // Focus lại vào ô chọn lớp
                if (classSelect.tomselect) classSelect.tomselect.focus();
                return;
            }

            // Validate HTML5 mặc định (Tiêu đề, v.v)
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Xử lý gửi Form
            actionInput.value = actionType;

            if (actionType === 'send') {
                let msg = {!! Auth::user()->role_id == 1
                    ? "'Thông báo sẽ được <b>ĐĂNG LÊN BẢNG TIN VÀ GỬI EMAIL NGAY LẬP TỨC</b>. Sau khi xuất bản sẽ <b>KHÔNG THỂ CHỈNH SỬA</b>. Bạn chắc chắn chứ?'"
                    : "'Hệ thống sẽ gửi thông báo này cho Admin xét duyệt. Bạn có chắc chắn nội dung đã hoàn thiện?'" !!};

                showConfirm('Xác nhận Xuất bản', msg, function() {
                    document.getElementById('form-action').value = 'send';
                    form.submit();
                }, 'primary');
            } else {
                document.getElementById('form-action').value = 'draft';
                form.submit();
            }
        }
    </script>
@endsection
