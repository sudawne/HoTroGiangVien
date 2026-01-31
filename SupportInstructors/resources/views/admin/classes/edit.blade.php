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
            // --- 1. KHAI BÁO BIẾN TOÀN CỤC ---
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const formClass = document.getElementById('editClassForm');
            const loadingModal = document.getElementById('loadingModal');
            const btnPreSubmit = document.getElementById('btn-pre-submit');

            // Modal & Input Import Mail
            const confirmImportMailModal = document.getElementById('confirmImportMailModal');
            const btnNoMailImport = document.getElementById('btn-no-mail-import');
            const btnYesMailImport = document.getElementById('btn-yes-mail-import');
            const sendEmailImportInput = document.getElementById('send_email_import_input');

            // Các biến cho bảng sinh viên
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const btnExportExcel = document.getElementById('btn-export-excel');
            const studentCountSpan = document.getElementById('student-count');

            // Modal Đa năng
            const universalModal = document.getElementById('universalModal');
            const uniTitle = document.getElementById('uni-modal-title');
            const uniDesc = document.getElementById('uni-modal-desc');
            const uniBtnConfirm = document.getElementById('btn-uni-confirm');
            const uniBtnCancel = document.getElementById('btn-uni-cancel');
            const uniBtnText = document.getElementById('uni-modal-btn-text');
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');
            let pendingCallback = null;

            // --- 2. HÀM MODAL ĐA NĂNG ---
            function showConfirm({
                title,
                message,
                btnText,
                btnColor = 'blue',
                icon = 'help',
                callback
            }) {
                uniTitle.innerText = title;
                uniDesc.innerText = message;
                uniBtnText.innerText = btnText;
                uniIcon.innerText = icon;
                pendingCallback = callback;

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

                // Reset class cũ và thêm class mới
                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 ${style.btn}`;
                uniIcon.className = `material-symbols-outlined text-[24px] ${style.icon}`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center ${style.bg}`;

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
                    universalModal.classList.add('hidden');
                    pendingCallback = null;
                });
            }

            // --- 3. LOGIC UPLOAD FILE PREVIEW ---
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
                                    'Cảnh báo: File chứa Mã sinh viên trùng (dòng đỏ). Vui lòng kiểm tra lại!';
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

            // --- 4. LOGIC SUBMIT FORM CẬP NHẬT LỚP ---
            function submitClassForm() {
                loadingModal.classList.remove('hidden');
                btnPreSubmit.disabled = true;
                btnPreSubmit.innerHTML =
                    '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang xử lý...';
                formClass.submit();
            }

            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    if (fileInput.files.length > 0) {
                        // Nếu có file -> Hỏi gửi mail cho danh sách import
                        confirmImportMailModal.classList.remove('hidden');
                    } else {
                        // Nếu không có file -> Hỏi xác nhận lưu bình thường
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

            if (btnNoMailImport) {
                btnNoMailImport.addEventListener('click', () => {
                    sendEmailImportInput.value = "0";
                    confirmImportMailModal.classList.add('hidden');
                    submitClassForm();
                });
            }
            if (btnYesMailImport) {
                btnYesMailImport.addEventListener('click', () => {
                    sendEmailImportInput.value = "1";
                    confirmImportMailModal.classList.add('hidden');
                    submitClassForm();
                });
            }

            // --- 5. HÀM KHỞI TẠO SỰ KIỆN CHO BẢNG SINH VIÊN (QUAN TRỌNG CHO AJAX) ---
            function initStudentTableEvents() {
                // A. Sự kiện Checkbox Select All
                const studentCheckboxes = document.querySelectorAll('.student-checkbox');

                function toggleSendButtonState() {
                    const anyChecked = Array.from(document.querySelectorAll('.student-checkbox')).some(cb => cb
                        .checked);
                    if (btnSendSelectedEmail) {
                        btnSendSelectedEmail.disabled = !anyChecked;
                        btnSendSelectedEmail.classList.toggle('opacity-50', !anyChecked);
                        btnSendSelectedEmail.classList.toggle('cursor-not-allowed', !anyChecked);
                    }
                }

                if (selectAll) {
                    selectAll.checked = false; // Reset khi reload bảng
                    selectAll.onclick = function() {
                        const isChecked = this.checked;
                        document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = isChecked);
                        toggleSendButtonState();
                    };
                }

                studentCheckboxes.forEach(cb => {
                    cb.addEventListener('change', toggleSendButtonState);
                });
                toggleSendButtonState(); // Init state

                // B. Sự kiện Nút Sửa
                document.querySelectorAll('.btn-edit-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const fullname = this.getAttribute('data-fullname');
                        const email = this.getAttribute('data-email');
                        const dob = this.getAttribute('data-dob');
                        const status = this.getAttribute('data-status');

                        const editModal = document.getElementById('editStudentModal');
                        const formEdit = document.getElementById('formEditStudent');

                        formEdit.action = `/admin/students/${id}`;
                        document.getElementById('edit_fullname').value = fullname;
                        document.getElementById('edit_email').value = email;
                        document.getElementById('edit_dob').value = dob;
                        document.getElementById('edit_status').value = status;

                        editModal.classList.remove('hidden');
                    });
                });

                // C. Sự kiện Nút Xóa
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        const studentCode = this.getAttribute('data-code');
                        showConfirm({
                            title: 'Xóa Sinh Viên?',
                            message: `Bạn có chắc chắn muốn xóa sinh viên ${studentCode}? Hành động này không thể hoàn tác.`,
                            btnText: 'Xóa ngay',
                            btnColor: 'red',
                            icon: 'warning',
                            callback: function() {
                                form.submit();
                            }
                        });
                    });
                });

                // D. Sự kiện Nút Gửi Mail Cá Nhân
                document.querySelectorAll('.btn-send-single-email').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const studentId = this.getAttribute('data-id');
                        showConfirm({
                            title: 'Gửi Email Cá Nhân',
                            message: 'Gửi email thông tin tài khoản cho sinh viên này?',
                            btnText: 'Gửi luôn',
                            btnColor: 'blue',
                            icon: 'forward_to_inbox',
                            callback: function() {
                                sendEmailsAjax([studentId]);
                            }
                        });
                    });
                });
            }

            // Gọi lần đầu khi trang load xong
            initStudentTableEvents();

            // --- 6. LOGIC TÌM KIẾM LIVE (AJAX) ---
            let debounceTimer;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value;

                    // UI Loading
                    searchSpinner.classList.remove('hidden');
                    tableOverlay.classList.remove('hidden');

                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        // Update URL
                        const url = new URL(window.location.href);
                        if (query) {
                            url.searchParams.set('search', query);
                        } else {
                            url.searchParams.delete('search');
                        }
                        // Reset page về 1 khi search
                        url.searchParams.delete('page');
                        window.history.pushState({}, '', url);

                        // Fetch Data
                        fetch(url, {
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                // Replace nội dung bảng và phân trang
                                if (tableBody) tableBody.innerHTML = data.html;
                                if (paginationLinks) paginationLinks.innerHTML = data
                                    .pagination;
                                if (studentCountSpan) studentCountSpan.innerText =
                                    `(${data.total})`;

                                // Re-bind events cho các phần tử mới
                                initStudentTableEvents();
                            })
                            .catch(err => console.error('Lỗi tìm kiếm:', err))
                            .finally(() => {
                                searchSpinner.classList.add('hidden');
                                tableOverlay.classList.add('hidden');
                            });
                    }, 400); // Debounce 400ms
                });
            }

            // --- 7. CÁC NÚT CHỨC NĂNG KHÁC ---

            // Gửi Mail Hàng Loạt
            if (btnSendSelectedEmail) {
                btnSendSelectedEmail.addEventListener('click', function() {
                    const selectedIds = Array.from(document.querySelectorAll('.student-checkbox'))
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);

                    if (selectedIds.length === 0) return;

                    showConfirm({
                        title: 'Gửi Email Hàng Loạt',
                        message: `Bạn có chắc muốn gửi thông tin tài khoản cho ${selectedIds.length} sinh viên đã chọn?`,
                        btnText: 'Gửi ngay',
                        btnColor: 'blue',
                        icon: 'send',
                        callback: function() {
                            sendEmailsAjax(selectedIds);
                        }
                    });
                });
            }

            // Xuất Excel
            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    showConfirm({
                        title: 'Xuất danh sách sinh viên',
                        message: 'Bạn có muốn tải xuống file Excel danh sách sinh viên lớp này không?',
                        btnText: 'Tải xuống',
                        btnColor: 'green',
                        icon: 'download',
                        callback: function() {
                            window.location.href = url;
                        }
                    });
                });
            }

            // Hàm AJAX gửi mail chung
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
                }).then(response => response.json()).then(data => {
                    loadingModal.classList.add('hidden');
                    alert(data.message || 'Đã thêm vào hàng đợi gửi mail!');
                }).catch(error => {
                    loadingModal.classList.add('hidden');
                    console.error('Error:', error);
                    alert('Lỗi khi gửi yêu cầu.');
                });
            }
        });
    </script>
@endsection
