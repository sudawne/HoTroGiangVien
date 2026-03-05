@extends('layouts.admin')
@section('title', 'Cập nhật Thông báo')

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

    <div class="w-full px-4 py-6">
        <div class="mb-4">
            <a href="{{ route('admin.notifications.index') }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined !text-[18px]">arrow_back</span> Quay lại danh sách
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 text-lg flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">edit_document</span> Cập nhật Bài đăng
                </h3>
                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded border border-yellow-200">
                    Đang {{ $notification->status == 'draft' ? 'Bản nháp' : 'Chờ duyệt' }}
                </span>
            </div>

            <form id="notification-form" action="{{ route('admin.notifications.update', $notification->id) }}"
                method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" id="form-action" value="draft">

                <div class="p-5 border border-blue-100 bg-blue-50/30 rounded-sm">
                    <label class="block text-sm font-bold text-slate-800 mb-3">Phạm vi gửi thông báo <span
                            class="text-red-500">*</span></label>
                    <div class="flex flex-wrap gap-8 mb-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_audience" value="all"
                                class="w-4 h-4 text-primary focus:ring-primary"
                                {{ old('target_audience', $notification->target_audience) == 'all' ? 'checked' : '' }}
                                onclick="document.getElementById('classSelectBox').classList.add('hidden')">
                            <span class="font-medium text-slate-700">Tất cả sinh viên Toàn Khoa</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="target_audience" value="class"
                                class="w-4 h-4 text-primary focus:ring-primary"
                                {{ old('target_audience', $notification->target_audience) == 'class' ? 'checked' : '' }}
                                onclick="document.getElementById('classSelectBox').classList.remove('hidden')">
                            <span class="font-medium text-slate-700">Chỉ gửi cho một Lớp cụ thể</span>
                        </label>
                    </div>

                    <div id="classSelectBox"
                        class="{{ old('target_audience', $notification->target_audience) == 'class' ? '' : 'hidden' }} transition-all duration-300">
                        <select name="class_id"
                            class="w-full md:w-1/2 px-4 py-2.5 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                            <option value="">-- Click để chọn lớp học --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}"
                                    {{ old('class_id', $notification->class_id) == $c->id ? 'selected' : '' }}>
                                    {{ $c->code }} - {{ $c->name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Tiêu đề bài viết <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $notification->title) }}" required
                        class="w-full px-4 py-3 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-base font-medium">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Mức độ ưu tiên</label>
                        <select name="type"
                            class="w-full px-4 py-3 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary">
                            <option value="info" {{ old('type', $notification->type) == 'info' ? 'selected' : '' }}>🔵
                                Thông tin chung (Bình thường)</option>
                            <option value="warning" {{ old('type', $notification->type) == 'warning' ? 'selected' : '' }}>
                                🟡 Cảnh báo / Nhắc nhở</option>
                            <option value="urgent" {{ old('type', $notification->type) == 'urgent' ? 'selected' : '' }}>🔴
                                Khẩn cấp</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-2">Cập nhật tài liệu mới (Ghi đè)</label>
                        <input type="file" name="attachment" accept=".pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-4 file:border-0 file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer border border-slate-300 rounded-sm">
                        @if ($notification->attachment_url)
                            <div class="mt-3 p-2 bg-blue-50 border border-blue-100 rounded text-xs">
                                <span class="text-slate-500">Đang đính kèm:</span><br>
                                <a href="{{ asset('storage/' . $notification->attachment_url) }}" target="_blank"
                                    class="font-bold text-blue-600 flex items-center gap-1 mt-1">
                                    <span class="material-symbols-outlined !text-[14px]">description</span> <span
                                        class="truncate">{{ $notification->attachment_name }}</span>
                                </a>
                            </div>
                        @endif
                        @error('attachment')
                            <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 mt-2">
                        <label class="flex items-center gap-2 cursor-pointer w-max">
                            <input type="checkbox" name="allow_comments" value="1"
                                {{ old('allow_comments', $notification->allow_comments) ? 'checked' : '' }}
                                class="w-4 h-4 text-primary focus:ring-primary rounded border-slate-300">
                            <span class="font-medium text-slate-700 text-sm">Cho phép mọi người bình luận dưới bài viết
                                này</span>
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-800 mb-2">Nội dung chi tiết <span
                            class="text-red-500">*</span></label>
                    <textarea id="editor" name="message">{{ old('message', $notification->message) }}</textarea>
                    @error('message')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-6 border-t border-slate-100 flex flex-wrap justify-end gap-3">
                    <button type="button" onclick="submitForm('draft')"
                        class="px-6 py-3 bg-slate-200 text-slate-700 rounded-sm font-bold hover:bg-slate-300 transition-colors">
                        Lưu Nháp Tiếp
                    </button>
                    <button type="button" onclick="submitForm('send')"
                        class="px-6 py-3 bg-primary text-white rounded-sm font-bold hover:bg-primary/90 flex items-center gap-2 shadow-md transition-colors">
                        <span class="material-symbols-outlined !text-[20px]">save</span>
                        {{ Auth::user()->role_id == 1 ? 'Lưu Lại & Xuất Bản' : 'Gửi Admin Duyệt Lại' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('admin.classes.partials.universal_confirm_modal')

    <script>
        let myEditor;

        document.addEventListener('DOMContentLoaded', function() {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList',
                        'blockQuote', '|', 'undo', 'redo'
                    ],
                    heading: {
                        options: [{
                                model: 'paragraph',
                                title: 'Paragraph',
                                class: 'ck-heading_paragraph'
                            },
                            {
                                model: 'heading1',
                                view: 'h3',
                                title: 'Tiêu đề nhỏ',
                                class: 'ck-heading_heading1'
                            }
                        ]
                    }
                })
                .then(editor => {
                    myEditor = editor;
                })
                .catch(error => {
                    console.error(error);
                });
        });

        function submitForm(actionType) {
            let form = document.getElementById('notification-form');
            let actionInput = document.getElementById('form-action');

            if (myEditor) {
                document.querySelector('#editor').value = myEditor.getData();
            }

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            actionInput.value = actionType;

            if (actionType === 'send') {
                let msg = {!! Auth::user()->role_id == 1
                    ? "'Hành động này sẽ <b>XUẤT BẢN NGAY LẬP TỨC</b> và <b>GỬI EMAIL</b>. Bạn chắc chắn chứ?'"
                    : "'Cập nhật nội dung và Gửi yêu cầu cho Admin xét duyệt lại?'" !!};
                showConfirm('Xác nhận Cập nhật', msg, function() {
                    document.getElementById('form-action').value = 'send';
                    form.submit();
                }, 'primary');
            } else {
                document.getElementById('form-action').value = 'draft';
                form.submit();
            }
        }

        function showConfirm(title, message, callback, type = 'primary') {
            const modal = document.getElementById('universalModal');
            document.getElementById('uni-modal-title').innerText = title;
            document.getElementById('uni-modal-desc').innerHTML = message;
            const btnConfirm = document.getElementById('btn-uni-confirm');

            btnConfirm.onclick = () => {
                modal.classList.add('hidden');
                callback();
            };

            const icon = document.getElementById('uni-modal-icon');
            const iconBg = document.getElementById('uni-modal-icon-bg');

            if (type === 'danger') {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-red-600 hover:bg-red-700 flex items-center gap-2";
                icon.innerText = 'warning';
                icon.className = 'material-symbols-outlined text-[24px] text-red-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100';
            } else {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-blue-600 hover:bg-blue-700 flex items-center gap-2";
                icon.innerText = 'help';
                icon.className = 'material-symbols-outlined text-[24px] text-blue-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100';
            }

            modal.classList.remove('hidden');
            modal.removeAttribute('style');
        }
    </script>
@endsection
