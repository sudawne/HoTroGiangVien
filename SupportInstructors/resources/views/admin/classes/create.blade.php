@extends('layouts.admin')
@section('title', 'Thêm Lớp học mới')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[16px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Thêm Lớp Mới</h1>
                    <p class="text-xs text-slate-500">Nhập thông tin lớp học vào hệ thống</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">school</span> Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data" class="p-6"
                id="createClassForm" novalidate>
                @csrf

                <input type="hidden" name="send_email" id="send_email_input" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="VD: 20DTHA1" required
                            class="w-full pl-3 pr-3 py-2.5 border {{ $errors->has('code') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        @error('code')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year') }}" required
                            placeholder="VD: 2020-2024"
                            class="w-full px-3 py-2.5 border {{ $errors->has('academic_year') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors text-sm">
                        @error('academic_year')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="VD: Đại học Công nghệ thông tin K20A"
                            class="w-full px-3 py-2.5 border {{ $errors->has('name') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors text-sm">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Đơn vị quản lý
                        </label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-slate-600 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa CNTT' }}
                        </div>
                        <input type="hidden" name="department_id" value="{{ $department->id ?? 1 }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border {{ $errors->has('advisor_id') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors text-sm cursor-pointer">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}" {{ old('advisor_id') == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('advisor_id')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <div
                            class="p-4 bg-blue-50 border border-blue-100 rounded-sm {{ $errors->has('student_file') ? 'border-red-500 bg-red-50' : '' }}">
                            <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 !text-[18px]">upload_file</span>
                                Import Danh sách Sinh viên (Tùy chọn)
                            </label>

                            <input type="file" name="student_file" id="student_file_input"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

                            @error('student_file')
                                <div class="mt-2 text-red-600 text-sm font-bold flex items-start gap-1">
                                    <span class="material-symbols-outlined !text-[16px]">warning</span>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror

                            <p class="text-xs text-slate-500 mt-2 ml-1">
                                Hỗ trợ file .xlsx, .csv. <span class="font-bold">Lưu ý:</span> Nếu mã SV bị trùng, quá trình
                                tạo lớp sẽ bị hủy.
                            </p>

                            <p id="upload-error" class="text-red-500 text-xs mt-2 hidden font-bold"></p>
                            <div id="preview-area"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.classes.index') }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 text-sm">Hủy
                        bỏ</a>
                    <button type="button" id="btn-pre-submit"
                        class="px-5 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 shadow-sm flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined !text-[16px]">save</span> Lưu Lớp Học
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- INCLUDE MODAL ĐA NĂNG --}}
    @include('admin.classes.partials.universal_confirm_modal')
    @include('admin.classes.partials.loading_modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const form = document.getElementById('createClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');
            const sendEmailInput = document.getElementById('send_email_input');
            const loadingModal = document.getElementById('loadingModal');

            // --- 1. LOGIC MODAL ĐA NĂNG ---
            const universalModal = document.getElementById('universalModal');
            const uniTitle = document.getElementById('uni-modal-title');
            const uniDesc = document.getElementById('uni-modal-desc');
            const uniBtnConfirm = document.getElementById('btn-uni-confirm');
            const uniBtnCancel = document.getElementById('btn-uni-cancel');
            const uniBtnText = document.getElementById('uni-modal-btn-text');
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');
            let pendingCallback = null;
            let cancelCallback = null; // Thêm callback cho nút Hủy

            function showConfirm({
                title,
                message,
                btnText,
                btnColor = 'blue',
                icon = 'help',
                callback,
                onCancel = null
            }) {
                uniTitle.innerText = title;
                uniDesc.innerText = message;
                uniBtnText.innerText = btnText;
                uniIcon.innerText = icon;
                pendingCallback = callback;
                cancelCallback = onCancel;

                const colors = {
                    blue: {
                        btn: 'bg-blue-600 hover:bg-blue-700',
                        icon: 'text-blue-600',
                        bg: 'bg-blue-100'
                    },
                    red: {
                        btn: 'bg-red-600 hover:bg-red-700',
                        icon: 'text-red-600',
                        bg: 'bg-red-100'
                    },
                    green: {
                        btn: 'bg-green-600 hover:bg-green-700',
                        icon: 'text-green-600',
                        bg: 'bg-green-100'
                    },
                };
                const style = colors[btnColor] || colors.blue;

                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 ${style.btn}`;
                uniIcon.className = `material-symbols-outlined text-[24px] ${style.icon}`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center ${style.bg}`;

                // Đổi text nút Hủy tùy ngữ cảnh (Ví dụ: "Không gửi" thay vì "Hủy bỏ")
                const cancelBtn = document.getElementById('btn-uni-cancel');
                if (onCancel) {
                    cancelBtn.innerText = 'Không gửi (Chỉ tạo)';
                } else {
                    cancelBtn.innerText = 'Hủy bỏ';
                }

                universalModal.classList.remove('hidden');
            }

            if (uniBtnConfirm) {
                uniBtnConfirm.addEventListener('click', function() {
                    if (pendingCallback) pendingCallback();
                    universalModal.classList.add('hidden');
                });
            }
            if (uniBtnCancel) {
                uniBtnCancel.addEventListener('click', function() {
                    if (cancelCallback)
                cancelCallback(); // Chạy hàm cancel nếu có (để submit form mà ko gửi mail)
                    universalModal.classList.add('hidden');
                    pendingCallback = null;
                    cancelCallback = null;
                });
            }

            // --- 2. PREVIEW FILE ---
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) {
                        previewArea.innerHTML = '';
                        return;
                    }
                    let formData = new FormData();
                    formData.append('file', file);
                    previewArea.innerHTML = `
                        <div class="mt-4 text-center text-slate-500 text-sm flex items-center justify-center gap-2 py-4">
                            <span class="animate-spin material-symbols-outlined text-blue-600 !text-[18px]">progress_activity</span> 
                            Đang đọc dữ liệu...
                        </div>`;
                    errorArea.classList.add('hidden');
                    fetch('{{ route('admin.classes.upload.preview') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                previewArea.innerHTML = '';
                                errorArea.innerText = data.error;
                                errorArea.classList.remove('hidden');
                            } else {
                                previewArea.innerHTML = data.html;
                                if (data.hasError) {
                                    errorArea.innerText =
                                        'Cảnh báo: File có chứa Mã sinh viên đã tồn tại (dòng màu đỏ). Vui lòng kiểm tra lại!';
                                    errorArea.classList.remove('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            previewArea.innerHTML = '';
                            errorArea.innerText = 'Có lỗi xảy ra khi tải file.';
                            errorArea.classList.remove('hidden');
                        });
                });
            }

            // --- 3. SUBMIT FORM ---
            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    // Nếu có file -> Hỏi gửi mail (Dùng Modal Đa năng)
                    if (fileInput.files.length > 0) {
                        showConfirm({
                            title: 'Gửi thông tin tài khoản?',
                            message: 'Bạn có muốn hệ thống tự động gửi email (MSSV & Mật khẩu) cho danh sách sinh viên vừa import không?',
                            btnText: 'Đồng ý gửi',
                            btnColor: 'blue',
                            icon: 'mark_email_unread',
                            callback: function() {
                                sendEmailInput.value = "1"; // Có gửi
                                submitForm();
                            },
                            onCancel: function() {
                                sendEmailInput.value = "0"; // Không gửi
                                submitForm();
                            }
                        });
                    } else {
                        // Nếu không có file -> Submit luôn (hoặc hỏi xác nhận lưu nếu muốn)
                        submitForm();
                    }
                });
            }

            function submitForm() {
                loadingModal.classList.remove('hidden');
                btnPreSubmit.disabled = true;
                btnPreSubmit.innerHTML =
                    '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang xử lý...';
                form.submit();
            }
        });
    </script>
@endsection
