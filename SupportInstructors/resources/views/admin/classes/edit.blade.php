@extends('layouts.admin')
@section('title', 'Cập nhật Lớp học')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[16px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Cập nhật Lớp học</h1>
                    <p class="text-xs text-slate-500">Chỉnh sửa thông tin lớp {{ $class->code }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">edit_square</span> Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6" id="editClassForm">
                @csrf
                @method('PUT')

                <input type="hidden" name="send_email" id="send_email_import_input" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $class->code) }}" required
                            class="w-full pl-3 pr-3 py-2.5 border {{ $errors->has('code') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        @error('code')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required
                            class="w-full px-3 py-2.5 border {{ $errors->has('academic_year') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors text-sm">
                        @error('academic_year')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
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
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border {{ $errors->has('advisor_id') ? 'border-red-500 focus:border-red-500' : 'border-slate-300 focus:border-primary' }} rounded-sm focus:ring-1 transition-colors text-sm cursor-pointer">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}"
                                    {{ old('advisor_id', $class->advisor_id) == $lec->id ? 'selected' : '' }}>
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
                                Import thêm Sinh viên (Tùy chọn)
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
                                cập nhật sẽ bị hủy.
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
                        <span class="material-symbols-outlined !text-[16px]">save</span> Cập nhật
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 flex justify-between items-center">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">group</span> Danh sách Sinh viên
                </h3>
                <button type="button" id="btn-send-selected-email"
                    class="px-4 py-2 bg-blue-600 text-white font-medium rounded-sm hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <span class="material-symbols-outlined !text-[16px]">send</span> Gửi thông tin tài khoản
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                            <th class="px-6 py-3 w-10 text-center">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3">MSSV</th>
                            <th class="px-6 py-3">Họ và Tên</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        @forelse ($students as $student)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50">
                                <td class="px-6 py-3 text-center">
                                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                                        class="student-checkbox rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                                </td>
                                <td class="px-6 py-3 font-mono text-slate-700 dark:text-slate-300">
                                    {{ $student->student_code }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800 dark:text-white">{{ $student->fullname }}
                                </td>
                                <td class="px-6 py-3 text-slate-500">{{ $student->user->email ?? 'Chưa có' }}</td>
                                <td class="px-6 py-3 text-right">
                                    <button type="button"
                                        class="btn-send-single-email p-1.5 text-blue-600 hover:bg-blue-50 rounded"
                                        data-id="{{ $student->id }}" title="Gửi mail riêng">
                                        <span class="material-symbols-outlined !text-[18px]">send</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Chưa có sinh viên nào
                                    trong lớp này.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="confirmImportMailModal" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-blue-600 !text-[24px]">mark_email_unread</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white">Gửi thông tin tài khoản?</h3>
                                <p class="text-sm text-slate-500 mt-1">
                                    Bạn có muốn hệ thống tự động gửi email (MSSV & Mật khẩu) cho danh sách sinh viên vừa
                                    import không?
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" id="btn-no-mail-import"
                                class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-sm hover:bg-slate-200 text-sm">
                                Không gửi (Chỉ tạo)
                            </button>
                            <button type="button" id="btn-yes-mail-import"
                                class="px-4 py-2 bg-blue-600 text-white font-medium rounded-sm hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined !text-[16px]">send</span> Đồng ý gửi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="confirmActionModal" class="fixed inset-0 z-[120] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                    <div class="p-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center">
                                <span class="material-symbols-outlined text-orange-600 !text-[24px]">help</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="confirm-modal-title">Xác
                                    nhận hành động</h3>
                                <p class="text-sm text-slate-500 mt-1" id="confirm-modal-desc">
                                    Bạn có chắc chắn muốn thực hiện hành động này không?
                                </p>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-3">
                            <button type="button" id="btn-cancel-action"
                                class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-sm hover:bg-slate-200 text-sm">
                                Hủy bỏ
                            </button>
                            <button type="button" id="btn-confirm-action"
                                class="px-4 py-2 bg-blue-600 text-white font-medium rounded-sm hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined !text-[16px]">check</span> Xác nhận
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('admin.classes.partials.loading_modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const form = document.getElementById('editClassForm');
            const loadingModal = document.getElementById('loadingModal');

            const btnPreSubmit = document.getElementById('btn-pre-submit');
            const confirmImportMailModal = document.getElementById('confirmImportMailModal');
            const btnNoMailImport = document.getElementById('btn-no-mail-import');
            const btnYesMailImport = document.getElementById('btn-yes-mail-import');
            const sendEmailImportInput = document.getElementById('send_email_import_input');

            const confirmActionModal = document.getElementById('confirmActionModal');
            const btnCancelAction = document.getElementById('btn-cancel-action');
            const btnConfirmAction = document.getElementById('btn-confirm-action');
            const confirmModalTitle = document.getElementById('confirm-modal-title');
            const confirmModalDesc = document.getElementById('confirm-modal-desc');

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
                                        'Cảnh báo: File có chứa Mã sinh viên đã tồn tại (dòng màu đỏ). Vui lòng kiểm tra lại trước khi lưu!';
                                    errorArea.classList.remove('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            previewArea.innerHTML = '';
                            errorArea.innerText = 'Có lỗi xảy ra khi tải file.';
                            errorArea.classList.remove('hidden');
                        });
                });
            }

            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }
                    if (fileInput.files.length > 0) {
                        confirmImportMailModal.classList.remove('hidden');
                    } else {
                        submitForm();
                    }
                });
            }

            if (btnNoMailImport) {
                btnNoMailImport.addEventListener('click', function() {
                    sendEmailImportInput.value = "0";
                    confirmImportMailModal.classList.add('hidden');
                    submitForm();
                });
            }

            if (btnYesMailImport) {
                btnYesMailImport.addEventListener('click', function() {
                    sendEmailImportInput.value = "1";
                    confirmImportMailModal.classList.add('hidden');
                    submitForm();
                });
            }

            function submitForm() {
                loadingModal.classList.remove('hidden');
                btnPreSubmit.disabled = true;
                btnPreSubmit.innerHTML =
                    '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang xử lý...';
                form.submit();
            }

            const selectAll = document.getElementById('select-all');
            const studentCheckboxes = document.querySelectorAll('.student-checkbox');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            let pendingAction = null;

            function toggleSendButton() {
                const anyChecked = Array.from(studentCheckboxes).some(cb => cb.checked);
                if (btnSendSelectedEmail) {
                    btnSendSelectedEmail.disabled = !anyChecked;
                    btnSendSelectedEmail.classList.toggle('opacity-50', !anyChecked);
                    btnSendSelectedEmail.classList.toggle('cursor-not-allowed', !anyChecked);
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    studentCheckboxes.forEach(cb => cb.checked = selectAll.checked);
                    toggleSendButton();
                });
            }

            studentCheckboxes.forEach(cb => {
                cb.addEventListener('change', toggleSendButton);
            });

            function openConfirmModal(title, desc, callback) {
                confirmModalTitle.innerText = title;
                confirmModalDesc.innerText = desc;
                confirmActionModal.classList.remove('hidden');
                pendingAction = callback;
            }

            btnConfirmAction.addEventListener('click', function() {
                if (pendingAction) pendingAction();
                confirmActionModal.classList.add('hidden');
            });

            btnCancelAction.addEventListener('click', function() {
                confirmActionModal.classList.add('hidden');
                pendingAction = null;
            });

            if (btnSendSelectedEmail) {
                btnSendSelectedEmail.addEventListener('click', function() {
                    const selectedIds = Array.from(studentCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);

                    if (selectedIds.length === 0) return;

                    openConfirmModal(
                        'Xác nhận gửi email',
                        `Bạn có chắc muốn gửi email thông tin tài khoản cho ${selectedIds.length} sinh viên đã chọn?`,
                        function() {
                            sendEmailsAjax(selectedIds);
                        }
                    );
                });
            }

            document.querySelectorAll('.btn-send-single-email').forEach(btn => {
                btn.addEventListener('click', function() {
                    const studentId = this.getAttribute('data-id');
                    openConfirmModal(
                        'Gửi email cá nhân',
                        'Gửi email thông tin tài khoản cho sinh viên này?',
                        function() {
                            sendEmailsAjax([studentId]);
                        }
                    );
                });
            });

            function sendEmailsAjax(ids) {
                loadingModal.classList.remove('hidden');

                fetch('{{ route('admin.classes.send_emails') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            student_ids: ids
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        loadingModal.classList.add('hidden');
                        alert(data.message || 'Đã gửi email thành công!');
                    })
                    .catch(error => {
                        loadingModal.classList.add('hidden');
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi gửi email.');
                    });
            }
        });
    </script>
@endsection
