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
                class="p-6" id="editClassForm" novalidate>
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
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Đơn vị quản
                            lý</label>
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
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">group</span> Danh sách Sinh viên
                    <span id="student-count" class="text-slate-500 text-sm font-normal">({{ $students->total() }})</span>
                </h3>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative flex-1 md:flex-none">
                        <input type="text" id="live-search-input" value="{{ request('search') }}"
                            placeholder="Tìm ngay (tên, MSSV)..."
                            class="pl-9 pr-4 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary w-full md:w-64 shadow-sm">
                        <span
                            class="material-symbols-outlined absolute left-2.5 top-2.5 text-slate-400 !text-[18px]">search</span>
                        <span id="search-spinner"
                            class="material-symbols-outlined absolute right-2.5 top-2.5 text-blue-500 !text-[18px] animate-spin hidden">progress_activity</span>
                    </div>

                    <a href="{{ route('admin.classes.export', $class->id) }}" id="btn-export-excel"
                        class="px-3 py-2 bg-green-600 text-white font-medium rounded-sm hover:bg-green-700 shadow-sm text-sm flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[18px]">download</span> Xuất Excel
                    </a>

                    <button type="button" id="btn-send-selected-email"
                        class="px-3 py-2 bg-blue-600 text-white font-medium rounded-sm hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined !text-[18px]">send</span> Gửi Mail TK
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto relative">
                <div id="table-loading-overlay"
                    class="absolute inset-0 bg-white/50 z-10 hidden flex items-center justify-center">
                    <div class="bg-white p-2 rounded shadow border border-slate-200 flex items-center gap-2">
                        <span class="animate-spin material-symbols-outlined text-primary">progress_activity</span>
                        <span class="text-sm font-medium text-slate-700">Đang lọc...</span>
                    </div>
                </div>

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
                            <th class="px-6 py-3">Ngày sinh</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body" class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        @include('admin.classes.partials.student_rows', ['students' => $students])
                    </tbody>
                </table>
            </div>
            <div id="pagination-links" class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL HỎI GỬI EMAIL RIÊNG BIỆT (DÀNH CHO LÚC IMPORT) --}}
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

    {{-- MODAL UNIVERSAL --}}
    @include('admin.classes.partials.universal_confirm_modal')

    {{-- MODAL SỬA SINH VIÊN --}}
    <div id="editStudentModal" class="fixed inset-0 z-[130] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    <form id="formEditStudent" method="POST" action="">
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person_edit</span> Cập nhật Sinh viên
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ và
                                        Tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="fullname" id="edit_fullname" required
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email
                                        Hệ thống</label>
                                    <input type="email" name="email" id="edit_email"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                    <p class="text-xs text-slate-400 mt-1">Lưu ý: Thay đổi email sẽ cập nhật tài khoản đăng
                                        nhập.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                        sinh</label>
                                    <input type="date" name="dob" id="edit_dob"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trạng
                                        thái <span class="text-red-500">*</span></label>
                                    <select name="status" id="edit_status"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                        <option value="studying">Đang học</option>
                                        <option value="reserved">Bảo lưu</option>
                                        <option value="dropped">Thôi học</option>
                                        <option value="graduated">Tốt nghiệp</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-800 px-4 py-3 flex justify-end gap-3 sm:px-6 border-t border-slate-100 dark:border-slate-700">
                            <button type="button"
                                onclick="document.getElementById('editStudentModal').classList.add('hidden')"
                                class="px-4 py-2 bg-white border border-slate-300 rounded-sm text-sm font-medium hover:bg-slate-50 text-slate-700">Hủy</button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-sm text-sm font-medium hover:bg-primary/90 flex items-center gap-2">
                                <span class="material-symbols-outlined !text-[16px]">save</span> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @include('admin.classes.partials.loading_modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. BIẾN ---
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const formClass = document.getElementById('editClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');
            const sendEmailInput = document.getElementById('send_email_import_input');

            // Table Variables
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnExportExcel = document.getElementById('btn-export-excel');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const studentCountSpan = document.getElementById('student-count');
            const filteredCountSpan = document.getElementById('filtered-count');

            // Modals
            const loadingModal = document.getElementById('loadingModal');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const loadingTitle = document.getElementById('loading-modal-title');

            const universalModal = document.getElementById('universalModal');
            const uniTitle = document.getElementById('uni-modal-title');
            const uniDesc = document.getElementById('uni-modal-desc');
            const uniBtnConfirm = document.getElementById('btn-uni-confirm');
            const uniBtnCancel = document.getElementById('btn-uni-cancel');
            const uniBtnText = document.getElementById('uni-modal-btn-text');
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');

            let pendingCallback = null;
            let cancelCallback = null;

            // --- 2. MODAL & HELPER ---
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

                // Reset nút
                uniBtnConfirm.classList.remove('hidden');
                uniBtnCancel.classList.remove('hidden');

                const btnCancel = document.getElementById('btn-uni-cancel');
                btnCancel.innerText = onCancel ? 'Không gửi (Chỉ tạo)' : 'Hủy bỏ';

                universalModal.classList.remove('hidden');
            }

            // Hàm thông báo thành công đẹp (Dùng lại universal modal)
            function showNotification(message) {
                uniTitle.innerText = "Thành công!";
                uniDesc.innerText = message;
                uniIcon.innerText = "check_circle";
                uniIconBg.className =
                    "flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-green-100 text-green-600";

                // Ẩn các nút bấm đi vì chỉ là thông báo
                uniBtnConfirm.classList.add('hidden');
                uniBtnCancel.classList.add('hidden');

                universalModal.classList.remove('hidden');

                // Tự tắt sau 2 giây
                setTimeout(() => {
                    universalModal.classList.add('hidden');
                    // Reset lại modal
                    uniBtnConfirm.classList.remove('hidden');
                    uniBtnCancel.classList.remove('hidden');
                }, 2000);
            }

            if (uniBtnConfirm) {
                uniBtnConfirm.addEventListener('click', function() {
                    if (pendingCallback) pendingCallback();
                    universalModal.classList.add('hidden');
                });
            }
            if (uniBtnCancel) {
                uniBtnCancel.addEventListener('click', function() {
                    if (cancelCallback) cancelCallback();
                    universalModal.classList.add('hidden');
                    pendingCallback = null;
                    cancelCallback = null;
                });
            }

            function chunkArray(myArray, chunk_size) {
                var results = [];
                while (myArray.length) {
                    results.push(myArray.splice(0, chunk_size));
                }
                return results;
            }

            // --- 3. CLASS UPDATE FORM ---
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) {
                        previewArea.innerHTML = '';
                        return;
                    }
                    let formData = new FormData();
                    formData.append('file', file);
                    previewArea.innerHTML =
                        `<div class="mt-4 text-center text-slate-500 text-sm flex items-center justify-center gap-2 py-4"><span class="animate-spin material-symbols-outlined text-blue-600 !text-[18px]">progress_activity</span> Đang đọc dữ liệu...</div>`;
                    errorArea.classList.add('hidden');

                    fetch('{{ route('admin.classes.upload.preview') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json()).then(data => {
                        if (data.error) {
                            previewArea.innerHTML = '';
                            errorArea.innerText = data.error;
                            errorArea.classList.remove('hidden');
                        } else {
                            previewArea.innerHTML = data.html;
                            if (data.hasError) {
                                errorArea.innerText =
                                    'Cảnh báo: File chứa Mã sinh viên trùng (dòng đỏ).';
                                errorArea.classList.remove('hidden');
                            }
                        }
                    }).catch(err => {
                        previewArea.innerHTML = '';
                        errorArea.innerText = 'Lỗi upload file.';
                        errorArea.classList.remove('hidden');
                    });
                });
            }

            function submitClassForm() {
                loadingModal.classList.remove('hidden');
                progressContainer.classList.add('hidden');
                loadingTitle.innerText = "Đang cập nhật...";
                btnPreSubmit.disabled = true;
                btnPreSubmit.innerHTML =
                    '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang xử lý...';
                formClass.submit();
            }

            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    if (!formClass.checkValidity()) {
                        formClass.reportValidity();
                        return;
                    }

                    if (fileInput.files.length > 0) {
                        showConfirm({
                            title: 'Gửi thông tin tài khoản?',
                            message: 'Bạn có muốn hệ thống tự động gửi email cho danh sách import không?',
                            btnText: 'Đồng ý gửi',
                            btnColor: 'blue',
                            icon: 'mark_email_unread',
                            callback: function() {
                                sendEmailInput.value = "1";
                                submitClassForm();
                            },
                            onCancel: function() {
                                sendEmailInput.value = "0";
                                submitClassForm();
                            }
                        });
                    } else {
                        showConfirm({
                            title: 'Lưu thay đổi',
                            message: 'Bạn có chắc chắn muốn cập nhật thông tin lớp học này không?',
                            btnText: 'Lưu ngay',
                            btnColor: 'blue',
                            icon: 'save',
                            callback: submitClassForm
                        });
                    }
                });
            }

            // --- 4. TABLE EVENTS ---
            function initStudentTableEvents() {
                const checkboxes = document.querySelectorAll('.student-checkbox');

                function toggleSendBtn() {
                    const any = Array.from(checkboxes).some(cb => cb.checked);
                    if (btnSendSelectedEmail) {
                        btnSendSelectedEmail.disabled = !any;
                        btnSendSelectedEmail.classList.toggle('opacity-50', !any);
                        btnSendSelectedEmail.classList.toggle('cursor-not-allowed', !any);
                    }
                }
                if (selectAll) {
                    selectAll.checked = false;
                    selectAll.onclick = function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        toggleSendBtn();
                    };
                }
                checkboxes.forEach(cb => cb.addEventListener('change', toggleSendBtn));
                toggleSendBtn();

                // Edit
                document.querySelectorAll('.btn-edit-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const formEdit = document.getElementById('formEditStudent');
                        const editModal = document.getElementById('editStudentModal');

                        formEdit.action = `/admin/students/${id}`;
                        document.getElementById('edit_fullname').value = this.getAttribute(
                            'data-fullname');
                        document.getElementById('edit_email').value = this.getAttribute(
                            'data-email');
                        document.getElementById('edit_dob').value = this.getAttribute('data-dob');
                        document.getElementById('edit_status').value = this.getAttribute(
                            'data-status');

                        editModal.classList.remove('hidden');
                    });
                });

                // Delete
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        showConfirm({
                            title: 'Xóa Sinh Viên?',
                            message: `Hành động này không thể hoàn tác.`,
                            btnText: 'Xóa ngay',
                            btnColor: 'red',
                            icon: 'warning',
                            callback: () => form.submit()
                        });
                    });
                });

                // Single Email
                document.querySelectorAll('.btn-send-single-email').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        showConfirm({
                            title: 'Gửi Email',
                            message: 'Gửi thông tin tài khoản cho sinh viên này?',
                            btnText: 'Gửi',
                            btnColor: 'blue',
                            icon: 'send',
                            callback: () => sendEmailsAjax([id])
                        });
                    });
                });
            }
            initStudentTableEvents();

            // Live Search
            let debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value;
                    searchSpinner.classList.remove('hidden');
                    tableOverlay.classList.remove('hidden');

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const url = new URL(window.location.href);
                        if (query) url.searchParams.set('search', query);
                        else url.searchParams.delete('search');
                        url.searchParams.delete('page');
                        window.history.pushState({}, '', url);

                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(res => res.json())
                            .then(data => {
                                tableBody.innerHTML = data.html;
                                paginationLinks.innerHTML = data.pagination;
                                if (studentCountSpan) studentCountSpan.innerText =
                                    `(${data.total})`;
                                initStudentTableEvents();
                            })
                            .finally(() => {
                                searchSpinner.classList.add('hidden');
                                tableOverlay.classList.add('hidden');
                            });
                    }, 400);
                });
            }

            // Export
            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    showConfirm({
                        title: 'Xuất Excel',
                        message: 'Tải xuống danh sách sinh viên?',
                        btnText: 'Tải xuống',
                        btnColor: 'green',
                        icon: 'download',
                        callback: () => window.location.href = url
                    });
                });
            }

            // Bulk Email
            if (btnSendSelectedEmail) {
                btnSendSelectedEmail.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox'))
                        .filter(cb => cb.checked).map(cb => cb.value);
                    if (ids.length === 0) return;

                    showConfirm({
                        title: 'Gửi Email Hàng Loạt',
                        message: `Gửi cho ${ids.length} sinh viên đã chọn?`,
                        btnText: 'Gửi ngay',
                        btnColor: 'blue',
                        icon: 'send',
                        callback: () => sendEmailsAjax(ids)
                    });
                });
            }

            // --- 5. BATCH SENDING LOGIC ---
            async function sendEmailsAjax(allIds) {
                loadingModal.classList.remove('hidden');
                progressContainer.classList.remove('hidden');

                const total = allIds.length;
                let processed = 0;
                const batches = chunkArray([...allIds], 3);

                loadingTitle.innerText = "Đang gửi Email...";
                progressBar.style.width = "0%";
                progressText.innerText = `Đã gửi 0/${total}`;

                for (const batch of batches) {
                    try {
                        const response = await fetch('{{ route('admin.classes.send_emails') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                student_ids: batch
                            })
                        });

                        if (!response.ok) throw new Error('Error');
                        processed += batch.length;

                        const percent = Math.round((processed / total) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressText.innerText = `Đã gửi ${processed}/${total} (${percent}%)`;

                    } catch (error) {
                        console.error('Batch error', error);
                        processed += batch.length;
                    }
                }

                // Tắt Loading và hiện thông báo đẹp
                setTimeout(() => {
                    loadingModal.classList.add('hidden');
                    progressContainer.classList.add('hidden');

                    showNotification(`Đã hoàn tất quy trình gửi ${processed}/${total} email!`);

                    // Reset checkbox
                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                    if (selectAll) selectAll.checked = false;
                }, 500);
            }
        });
    </script>
@endsection
