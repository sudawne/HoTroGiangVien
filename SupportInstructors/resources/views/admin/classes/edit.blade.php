@extends('layouts.admin')
@section('title', 'Cập nhật Lớp học')

@section('content')
    <div class="w-full px-4 py-6">
        {{-- HEADER (Giữ nguyên) --}}
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

        {{-- FORM UPDATE CLASS (Giữ nguyên nội dung Form) --}}
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
                    {{-- 1. Mã lớp --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $class->code) }}" required
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="code"></p>
                    </div>

                    {{-- 2. Niên khóa --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="academic_year"></p>
                    </div>

                    {{-- 3. Tên lớp --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="name"></p>
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    {{-- 4. Đơn vị quản lý --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Đơn vị quản
                            lý</label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-slate-600 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa CNTT' }}
                        </div>
                    </div>

                    {{-- 5. Cố vấn học tập --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm cursor-pointer">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}"
                                    {{ old('advisor_id', $class->advisor_id) == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="advisor_id"></p>
                    </div>

                    {{-- 6. Import File & Manual Add --}}
                    <div class="md:col-span-2 mt-2">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 !text-[20px]">group_add</span>
                                Thêm Sinh viên Mới vào Lớp (Import Excel hoặc Nhập tay)
                            </label>

                            {{-- Form thêm tay nhỏ --}}
                            <div class="bg-white p-3 border border-slate-200 rounded-sm mb-4 shadow-sm">
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Nhập nhanh sinh viên</h4>
                                <div class="flex flex-wrap gap-3 items-end">
                                    <div class="flex-1 min-w-[120px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Mã SV *</label>
                                        <input type="text" id="manual_mssv"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm uppercase focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Họ và Tên *</label>
                                        <input type="text" id="manual_name" placeholder="VD: Nguyễn Văn A"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div class="w-[130px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Ngày sinh</label>
                                        <input type="date" id="manual_dob"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm">
                                    </div>
                                    <div class="w-[110px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Trạng thái</label>
                                        <select id="manual_status"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm">
                                            <option value="studying">Đang học</option>
                                            <option value="reserved">Bảo lưu</option>
                                        </select>
                                    </div>
                                    <button type="button" id="btn-add-manual"
                                        class="px-4 py-1.5 bg-slate-800 text-white rounded text-sm font-medium hover:bg-slate-700 transition-colors flex items-center gap-1 shadow-sm">
                                        <span class="material-symbols-outlined !text-[16px]">add</span> Thêm
                                    </button>
                                </div>
                            </div>

                            {{-- Input File --}}
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase">Hoặc tải file lên (xlsx,
                                    csv):</span>
                            </div>
                            <input type="file" name="student_file_temp" id="student_file_input" accept=".xlsx, .csv"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

                            <p class="text-red-500 text-xs mt-1 error-msg" data-field="student_file"></p>
                            <p id="upload-error" class="text-red-500 text-xs mt-2 hidden font-bold"></p>

                            {{-- Preview Area cho sinh viên MỚI --}}
                            <div id="new-students-preview" class="mt-4"></div>
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

        {{-- LIST EXISTING STUDENTS TABLE (Giữ nguyên) --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">group</span> Danh sách Sinh viên Hiện
                    tại
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

                    <button type="button" id="btn-delete-selected"
                        class="hidden flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-sm hover:bg-red-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">delete</span>
                        <span id="btn-delete-text">Xóa đã chọn</span>
                    </button>

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

    {{-- MODALS --}}
    @include('admin.classes.partials.loading_modal')
    @include('admin.classes.partials.universal_confirm_modal')

    {{-- STUDENT EDIT MODAL (Giữ nguyên) --}}
    <div id="editStudentModal" class="fixed inset-0 z-[130] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    <form id="formEditStudent" method="POST" action="" novalidate>
                        @csrf
                        @method('PUT')
                        <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person_edit</span> Cập nhật Sinh viên
                            </h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ
                                        và
                                        Tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="fullname" id="edit_fullname" required
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                    <p class="text-red-500 text-xs mt-1 error-msg" data-field="fullname"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Email
                                        Hệ thống</label>
                                    <input type="email" name="email" id="edit_email"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                    <p class="text-red-500 text-xs mt-1 error-msg" data-field="email"></p>
                                    <p class="text-xs text-slate-400 mt-1">Lưu ý: Thay đổi email sẽ cập nhật tài khoản đăng
                                        nhập.</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                        sinh</label>
                                    <input type="date" name="dob" id="edit_dob"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                    <p class="text-red-500 text-xs mt-1 error-msg" data-field="dob"></p>
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
                                    <p class="text-red-500 text-xs mt-1 error-msg" data-field="status"></p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ----------------------------------------------------------------
            // 1. DOM Elements
            // ----------------------------------------------------------------
            const formClass = document.getElementById('editClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');
            // Elements cho tính năng Thêm mới/Import
            const fileInput = document.getElementById('student_file_input');
            const btnAddManual = document.getElementById('btn-add-manual');
            const newStudentsPreviewArea = document.getElementById('new-students-preview');
            const uploadErrorArea = document.getElementById('upload-error');
            const sendEmailInput = document.getElementById('send_email_import_input');

            // Elements cho bảng sinh viên hiện tại
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnExportExcel = document.getElementById('btn-export-excel');
            const btnDeleteSelected = document.getElementById('btn-delete-selected');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const studentCountSpan = document.getElementById('student-count');

            // Modals
            const loadingModal = document.getElementById('loadingModal');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const loadingTitle = document.getElementById('loading-modal-title');
            const loadingDesc = document.getElementById('loading-modal-desc');

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

            // List chứa sinh viên MỚI sẽ thêm
            let newStudentsList = [];

            // ----------------------------------------------------------------
            // 2. HELPER FUNCTIONS
            // ----------------------------------------------------------------
            function clearValidationErrors(formElement) {
                if (!formElement) return;
                formElement.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
                formElement.querySelectorAll('.border-red-500').forEach(el => {
                    el.classList.remove('border-red-500', 'focus:border-red-500');
                    el.classList.add('border-slate-300', 'focus:border-primary');
                });
            }

            function showFieldError(formElement, fieldName, message) {
                const inputElement = formElement.querySelector(`[name="${fieldName}"]`);
                const errorElement = formElement.querySelector(`.error-msg[data-field="${fieldName}"]`);
                if (inputElement) {
                    inputElement.classList.add('border-red-500');
                    inputElement.addEventListener('input', function() {
                        this.classList.remove('border-red-500');
                        if (errorElement) errorElement.innerText = '';
                    }, {
                        once: true
                    });
                }
                if (errorElement) errorElement.innerText = message;
            }

            function showServerValidationErrors(errors, formElement) {
                for (const [field, messages] of Object.entries(errors)) {
                    showFieldError(formElement, field, messages[0]);
                }
            }

            function forceShowUniversalModal() {
                if (universalModal) {
                    universalModal.classList.remove('hidden');
                    universalModal.removeAttribute('style');
                    universalModal.style.zIndex = '9999';
                }
            }

            function forceHideUniversalModal() {
                if (universalModal) {
                    universalModal.classList.add('hidden');
                    universalModal.setAttribute('style', 'display: none !important;');
                }
            }

            function showAlert(title, message) {
                uniTitle.innerText = title;
                uniDesc.innerText = message;
                uniBtnText.innerText = 'Đã hiểu';
                uniIcon.innerText = 'warning';
                pendingCallback = null;
                cancelCallback = null;
                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 bg-red-600 hover:bg-red-700`;
                uniIcon.className = `material-symbols-outlined text-[24px] text-red-600`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100`;
                uniBtnCancel.classList.add('hidden');
                uniBtnConfirm.classList.remove('hidden');
                forceShowUniversalModal();
            }

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
                    green: {
                        btn: 'bg-green-600 hover:bg-green-700',
                        icon: 'text-green-600',
                        bg: 'bg-green-100'
                    },
                    red: {
                        btn: 'bg-red-600 hover:bg-red-700',
                        icon: 'text-red-600',
                        bg: 'bg-red-100'
                    }
                };
                const style = colors[btnColor] || colors.blue;

                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 ${style.btn}`;
                uniIcon.className = `material-symbols-outlined text-[24px] ${style.icon}`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center ${style.bg}`;

                const btnCancelDOM = document.getElementById('btn-uni-cancel');
                btnCancelDOM.innerText = onCancel ? 'Không gửi (Chỉ tạo)' : 'Hủy bỏ';

                uniBtnConfirm.classList.remove('hidden');
                uniBtnCancel.classList.remove('hidden');
                forceShowUniversalModal();
            }

            function showNotification(message) {
                showAlert("Thành công!", message);
                setTimeout(() => forceHideUniversalModal(), 1500);
            }

            if (uniBtnConfirm) {
                uniBtnConfirm.addEventListener('click', function() {
                    forceHideUniversalModal();
                    setTimeout(() => {
                        if (pendingCallback) pendingCallback();
                    }, 400);
                });
            }
            if (uniBtnCancel) {
                uniBtnCancel.addEventListener('click', function() {
                    forceHideUniversalModal();
                    setTimeout(() => {
                        if (cancelCallback) cancelCallback();
                        pendingCallback = null;
                        cancelCallback = null;
                    }, 400);
                });
            }

            function chunkArray(myArray, chunk_size) {
                var results = [];
                var arr = [...myArray];
                while (arr.length) {
                    results.push(arr.splice(0, chunk_size));
                }
                return results;
            }

            // ----------------------------------------------------------------
            // 3. LOGIC MỚI: QUẢN LÝ DANH SÁCH SINH VIÊN MỚI (NEW STUDENTS)
            // ----------------------------------------------------------------
            function renderNewStudentsTable() {
                if (newStudentsList.length === 0) {
                    newStudentsPreviewArea.innerHTML = '';
                    return;
                }

                let html = `
                    <div class="mb-2 text-sm font-bold text-blue-700 flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[18px]">playlist_add</span>
                        Danh sách chuẩn bị thêm (${newStudentsList.length})
                    </div>
                    <table class="w-full text-left text-sm border-collapse bg-white shadow-sm rounded-sm overflow-hidden border border-slate-200 mb-4">
                        <thead class="bg-slate-100 border-b border-slate-200 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-3 py-2 w-10 text-center">STT</th>
                                <th class="px-3 py-2">Mã SV</th>
                                <th class="px-3 py-2">Họ Tên</th>
                                <th class="px-3 py-2">Ngày sinh</th>
                                <th class="px-3 py-2 text-center w-16">Xóa</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                `;

                newStudentsList.forEach((s, index) => {
                    const isDup = s.is_duplicate ? 'bg-red-50' : '';
                    const textDup = s.is_duplicate ?
                        '<span class="text-xs text-red-600 font-bold ml-1">(Trùng)</span>' : '';
                    html += `
                        <tr class="${isDup} hover:bg-slate-50">
                            <td class="px-3 py-2 text-center text-slate-500">${index + 1}</td>
                            <td class="px-3 py-2 font-medium">${s.mssv} ${textDup}</td>
                            <td class="px-3 py-2">${s.name}</td>
                            <td class="px-3 py-2">${s.dob || '-'}</td>
                            <td class="px-3 py-2 text-center">
                                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeNewStudent(${index})" title="Xóa dòng này">
                                    <span class="material-symbols-outlined !text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;

                if (newStudentsList.some(s => s.is_duplicate)) {
                    html +=
                        `<p class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1"><span class="material-symbols-outlined !text-[14px]">warning</span> Có sinh viên bị trùng mã trong danh sách chuẩn bị thêm. Vui lòng xóa dòng trùng để có thể tiếp tục.</p>`;
                }
                newStudentsPreviewArea.innerHTML = html;
            }

            window.removeNewStudent = function(index) {
                newStudentsList.splice(index, 1);
                renderNewStudentsTable();
            };

            // Sự kiện nút "Thêm" thủ công
            if (btnAddManual) {
                btnAddManual.addEventListener('click', () => {
                    const mssvInput = document.getElementById('manual_mssv');
                    const nameInput = document.getElementById('manual_name');
                    const dobInput = document.getElementById('manual_dob');
                    const statusInput = document.getElementById('manual_status');

                    const mssv = mssvInput.value.trim().toUpperCase();
                    const name = nameInput.value.trim();

                    if (!mssv || !name) {
                        showAlert("Thiếu thông tin", "Vui lòng nhập đầy đủ Mã Sinh Viên và Họ Tên.");
                        return;
                    }

                    // Check trùng trong danh sách MỚI
                    if (newStudentsList.some(s => s.mssv === mssv)) {
                        showAlert("Trùng dữ liệu", "Mã SV này đã tồn tại trong danh sách chuẩn bị thêm!");
                        return;
                    }

                    // Thêm vào danh sách
                    newStudentsList.push({
                        mssv: mssv,
                        name: name,
                        dob: dobInput.value,
                        status: statusInput.value,
                        is_duplicate: false
                    });

                    // Reset form
                    mssvInput.value = '';
                    nameInput.value = '';
                    dobInput.value = '';
                    renderNewStudentsTable();
                });
            }

            // Sự kiện File Upload
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) return;

                    let formData = new FormData();
                    formData.append('file', file);
                    uploadErrorArea.classList.add('hidden');

                    // Hiển thị loading tạm thời
                    newStudentsPreviewArea.innerHTML =
                        `<div class="mt-4 text-center text-slate-500 text-sm flex items-center justify-center gap-2 py-4"><span class="animate-spin material-symbols-outlined text-blue-600 !text-[18px]">progress_activity</span> Đang đọc file...</div>`;

                    fetch('{{ route('admin.classes.upload.preview') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json()).then(data => {
                        if (data.error) {
                            uploadErrorArea.innerText = data.error;
                            uploadErrorArea.classList.remove('hidden');
                            renderNewStudentsTable();
                        } else {
                            if (data.data && Array.isArray(data.data)) {
                                // Merge data từ file vào list hiện tại
                                data.data.forEach(item => {
                                    if (!newStudentsList.some(s => s.mssv === item.mssv)) {
                                        newStudentsList.push(item);
                                    }
                                });
                                renderNewStudentsTable();
                                fileInput.value = '';
                            } else {
                                uploadErrorArea.innerText = "Cấu trúc dữ liệu trả về không hợp lệ.";
                                uploadErrorArea.classList.remove('hidden');
                            }
                        }
                    }).catch(err => {
                        renderNewStudentsTable();
                        uploadErrorArea.innerText = 'Lỗi upload file.';
                        uploadErrorArea.classList.remove('hidden');
                    });
                });
            }

            // ----------------------------------------------------------------
            // 4. HÀM RELOAD TABLE (Được tách ra để tái sử dụng)
            // ----------------------------------------------------------------
            async function reloadStudentTable(url = null) {
                const currentUrl = url || window.location.href;
                searchSpinner.classList.remove('hidden');
                tableOverlay.classList.remove('hidden');

                try {
                    const response = await fetch(currentUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await response.json();

                    tableBody.innerHTML = data.html;
                    paginationLinks.innerHTML = data.pagination;
                    if (studentCountSpan) studentCountSpan.innerText = `(${data.total})`;

                    // Gán lại sự kiện cho các nút trong bảng vừa render
                    initStudentTableEvents();
                } catch (error) {
                    console.error("Lỗi tải lại bảng:", error);
                } finally {
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                }
            }

            // ----------------------------------------------------------------
            // 5. SUBMIT FORM UPDATE CLASS (Main Logic)
            // ----------------------------------------------------------------
            async function submitClassForm() {
                loadingModal.classList.remove('hidden');
                progressContainer.classList.add('hidden');
                loadingTitle.innerText = "Đang cập nhật...";
                loadingDesc.innerText = "Vui lòng chờ...";
                btnPreSubmit.disabled = true;
                btnPreSubmit.innerHTML =
                    '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang xử lý...';

                clearValidationErrors(formClass);

                try {
                    const formData = new FormData(formClass);
                    formData.delete('student_file_temp');

                    if (newStudentsList.length > 0) {
                        formData.set('students_list', JSON.stringify(newStudentsList));
                    }

                    const response = await fetch(formClass.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (response.status === 422) {
                        loadingModal.classList.add('hidden');
                        btnPreSubmit.disabled = false;
                        btnPreSubmit.innerHTML =
                            '<span class="material-symbols-outlined !text-[16px]">save</span> Cập nhật';
                        showServerValidationErrors(data.errors, formClass);
                        return;
                    }

                    if (!data.success) throw new Error(data.message || 'Có lỗi xảy ra');

                    // Gửi email nếu cần
                    if (sendEmailInput.value == "1" && data.new_student_ids && data.new_student_ids.length >
                        0) {
                        await sendEmailsInBatches(data.new_student_ids, null); // Truyền null vì không redirect
                    } else {
                        // Ẩn loading
                        loadingModal.classList.add('hidden');
                    }

                    // --- XỬ LÝ SAU KHI THÀNH CÔNG (Không redirect) ---
                    // 1. Reset nút submit
                    btnPreSubmit.disabled = false;
                    btnPreSubmit.innerHTML =
                        '<span class="material-symbols-outlined !text-[16px]">save</span> Cập nhật';

                    // 2. Xóa danh sách thêm mới
                    newStudentsList = [];
                    renderNewStudentsTable();
                    fileInput.value = '';

                    // 3. Reload bảng và Highlight
                    await reloadStudentTable();

                    // 4. Highlight các dòng mới thêm
                    if (data.new_student_ids && data.new_student_ids.length > 0) {
                        data.new_student_ids.forEach(id => {
                            // Tìm checkbox có value = id, rồi lấy tr cha
                            const checkbox = document.querySelector(`.student-checkbox[value="${id}"]`);
                            if (checkbox) {
                                const row = checkbox.closest('tr');
                                if (row) {
                                    row.classList.add('highlight-row');
                                    // Tự động scroll tới dòng đó nếu cần (optional)
                                    row.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                                }
                            }
                        });
                    }

                } catch (error) {
                    loadingModal.classList.add('hidden');
                    btnPreSubmit.disabled = false;
                    btnPreSubmit.innerHTML =
                        '<span class="material-symbols-outlined !text-[16px]">save</span> Cập nhật';
                    alert('Lỗi: ' + error.message);
                }
            }

            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    clearValidationErrors(formClass);
                    uploadErrorArea.classList.add('hidden');

                    let hasClientError = false;
                    const requiredInputs = formClass.querySelectorAll('[required]');
                    const customMessages = {
                        'code': 'Vui lòng nhập Mã lớp',
                        'academic_year': 'Vui lòng nhập Niên khóa',
                        'name': 'Vui lòng nhập Tên lớp',
                        'advisor_id': 'Vui lòng chọn Cố vấn học tập'
                    };

                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            const fieldName = input.getAttribute('name');
                            showFieldError(formClass, fieldName, customMessages[fieldName] ||
                                'Vui lòng nhập thông tin này');
                            hasClientError = true;
                        }
                    });

                    if (newStudentsList.some(s => s.is_duplicate)) {
                        showAlert("Dữ liệu không hợp lệ",
                            "Vui lòng xóa các sinh viên bị trùng mã (dòng màu đỏ) trong danh sách thêm mới trước khi lưu!"
                        );
                        return;
                    }

                    if (hasClientError) return;

                    if (newStudentsList.length > 0) {
                        showConfirm({
                            title: 'Gửi thông tin tài khoản?',
                            message: 'Bạn có muốn hệ thống tự động gửi email cho các sinh viên MỚI vừa thêm không?',
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

            // ----------------------------------------------------------------
            // 6. EXISTING TABLE LOGIC (Pagination, Search, Delete Existing)
            // ----------------------------------------------------------------
            function toggleActionBtns() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                const any = selectedCount > 0;

                if (btnSendSelectedEmail) {
                    btnSendSelectedEmail.disabled = !any;
                    btnSendSelectedEmail.classList.toggle('opacity-50', !any);
                    btnSendSelectedEmail.classList.toggle('cursor-not-allowed', !any);
                }
                if (btnDeleteSelected) {
                    if (any) {
                        btnDeleteSelected.classList.remove('hidden');
                        const textSpan = btnDeleteSelected.querySelector('span:not(.material-symbols-outlined)');
                        if (textSpan) textSpan.innerText = `Xóa (${selectedCount})`;
                    } else {
                        btnDeleteSelected.classList.add('hidden');
                    }
                }
            }

            function initStudentTableEvents() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                if (selectAll) {
                    selectAll.onclick = null;
                    selectAll.checked = false;
                    selectAll.onclick = function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        toggleActionBtns();
                    };
                }
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', toggleActionBtns);
                });
                toggleActionBtns();

                // Nút xóa từng dòng
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        const code = this.getAttribute('data-code');
                        const url = form.action;
                        showConfirm({
                            title: 'Xóa Sinh Viên?',
                            message: `Bạn có chắc muốn xóa sinh viên ${code} khỏi hệ thống?`,
                            btnText: 'Xóa ngay',
                            btnColor: 'red',
                            icon: 'warning',
                            callback: async () => {
                                try {
                                    const response = await fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: new URLSearchParams({
                                            '_method': 'DELETE'
                                        })
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        // Xóa dòng không cần reload
                                        const row = btn.closest('tr');
                                        if (row) row.remove();
                                        if (studentCountSpan) {
                                            let count = parseInt(studentCountSpan
                                                    .innerText.replace(/[()]/g, '')) ||
                                                0;
                                            if (count > 0) studentCountSpan.innerText =
                                                `(${count - 1})`;
                                        }
                                        // Optional: show toast notification nhỏ
                                    } else {
                                        alert("Lỗi: " + data.message);
                                    }
                                } catch (e) {
                                    alert("Có lỗi xảy ra khi xóa.");
                                }
                            }
                        });
                    });
                });

                // Các sự kiện Edit, Send Email Single ... (Giữ nguyên như cũ)
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
                        clearValidationErrors(formEdit);
                        editModal.classList.remove('hidden');
                    });
                });

                document.querySelectorAll('.btn-send-single-email').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        showConfirm({
                            title: 'Gửi Email',
                            message: 'Gửi thông tin tài khoản cho sinh viên này?',
                            btnText: 'Gửi',
                            btnColor: 'blue',
                            icon: 'send',
                            callback: () => sendEmailsInBatches([id], null)
                        });
                    });
                });
            }
            initStudentTableEvents();

            // Live Search (Sử dụng reloadStudentTable)
            if (searchInput) {
                let debounceTimer;
                searchInput.addEventListener('input', function() {
                    const query = this.value;
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        const url = new URL(window.location.href);
                        if (query) url.searchParams.set('search', query);
                        else url.searchParams.delete('search');
                        url.searchParams.delete('page');
                        window.history.pushState({}, '', url);

                        reloadStudentTable(url);
                    }, 400);
                });
            }

            // Edit Student Modal (Giữ nguyên)
            const formEditStudent = document.getElementById('formEditStudent');
            const modalEditStudent = document.getElementById('editStudentModal');
            if (formEditStudent) {
                formEditStudent.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    clearValidationErrors(formEditStudent);
                    loadingModal.classList.remove('hidden');
                    progressContainer.classList.add('hidden');
                    loadingTitle.innerText = "Đang cập nhật...";
                    const btnSubmit = this.querySelector('button[type="submit"]');
                    const originalBtnText = btnSubmit.innerHTML;
                    btnSubmit.disabled = true;
                    btnSubmit.innerHTML =
                        '<span class="material-symbols-outlined !text-[16px] animate-spin">progress_activity</span> Đang lưu...';

                    try {
                        const formData = new FormData(this);
                        formData.append('_method', 'PUT');
                        const response = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            },
                            body: formData
                        });
                        const data = await response.json();
                        if (response.status === 422) {
                            loadingModal.classList.add('hidden');
                            showServerValidationErrors(data.errors, formEditStudent);
                            return;
                        }
                        if (!data.success && response.status !== 200) throw new Error(data.message ||
                            "Lỗi cập nhật");
                        loadingModal.classList.add('hidden');
                        modalEditStudent.classList.add('hidden');

                        // Thay vì reload trang, ta reload table
                        reloadStudentTable();

                    } catch (error) {
                        loadingModal.classList.add('hidden');
                        alert("Lỗi: " + error.message);
                    } finally {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalBtnText;
                    }
                });
            }

            // Delete Selected
            if (btnDeleteSelected) {
                btnDeleteSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox')).filter(cb => cb
                        .checked).map(cb => cb.value);
                    if (ids.length === 0) return;
                    showConfirm({
                        title: 'Xóa ' + ids.length + ' Sinh Viên?',
                        message: 'Các sinh viên đã chọn sẽ bị chuyển vào thùng rác.',
                        btnText: 'Xóa tất cả',
                        btnColor: 'red',
                        icon: 'delete_forever',
                        callback: async () => {
                            loadingModal.classList.remove('hidden');
                            progressContainer.classList.add('hidden');
                            loadingTitle.innerText = "Đang xóa dữ liệu...";
                            try {
                                const response = await fetch(
                                    '{{ route('admin.students.bulk_destroy') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({
                                            ids: ids
                                        })
                                    });
                                const data = await response.json();
                                loadingModal.classList.add('hidden');
                                if (data.success) {
                                    ids.forEach(id => {
                                        const checkbox = document.querySelector(
                                            `.student-checkbox[value="${id}"]`);
                                        if (checkbox) checkbox.closest('tr').remove();
                                    });
                                    if (selectAll) selectAll.checked = false;
                                    toggleActionBtns();
                                } else {
                                    alert("Lỗi: " + data.message);
                                }
                            } catch (e) {
                                loadingModal.classList.add('hidden');
                                alert("Có lỗi xảy ra khi xóa nhiều.");
                            }
                        }
                    });
                });
            }

            // Export & Send Email (Giữ nguyên)
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

            if (btnSendSelectedEmail) {
                btnSendSelectedEmail.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox')).filter(cb => cb
                        .checked).map(cb => cb.value);
                    if (ids.length === 0) return;
                    showConfirm({
                        title: 'Gửi Email Hàng Loạt',
                        message: `Gửi cho ${ids.length} sinh viên đã chọn?`,
                        btnText: 'Gửi ngay',
                        btnColor: 'blue',
                        icon: 'send',
                        callback: () => sendEmailsInBatches(ids, null)
                    });
                });
            }

            // Send Mail Helper
            async function sendEmailsInBatches(studentIds, redirectUrl) {
                progressContainer.classList.remove('hidden');
                loadingTitle.innerText = "Đang gửi Email...";
                loadingDesc.innerText = "Vui lòng không tắt trình duyệt.";
                progressBar.style.width = "0%";
                const total = studentIds.length;
                let processed = 0;
                const batches = chunkArray(studentIds, 3);

                for (const batch of batches) {
                    try {
                        await fetch('{{ route('admin.classes.send_emails') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                student_ids: batch
                            })
                        });
                        processed += batch.length;
                        const percent = Math.round((processed / total) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressText.innerText = `Đã gửi ${processed}/${total}`;
                    } catch (err) {
                        console.error(err);
                    }
                }

                if (redirectUrl) {
                    setTimeout(() => window.location.href = redirectUrl, 1000);
                } else {
                    loadingModal.classList.add('hidden');
                    progressContainer.classList.add('hidden');
                    // Không cần showNotification nếu vừa submit form xong, logic đó đã xử lý ở trên
                    // Tuy nhiên nếu gọi độc lập thì cần reset check
                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                    if (selectAll) selectAll.checked = false;
                    toggleActionBtns();
                }
            }
        });
    </script>
@endsection
