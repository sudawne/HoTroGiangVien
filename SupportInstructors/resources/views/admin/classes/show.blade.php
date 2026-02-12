@extends('layouts.admin')
@section('title', 'Chi tiết Lớp ' . $class->code)

@section('content')
    <div class="w-full px-4 py-6">

        {{-- 1. HEADER --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 shadow-sm transition-colors">
                    <span class="material-symbols-outlined !text-[16px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        {{ $class->name }}
                        <span
                            class="text-sm font-mono font-normal bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600">
                            {{ $class->code }}
                        </span>
                    </h1>
                    <div class="flex items-center gap-4 mt-1 text-sm text-slate-500">
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[14px]">school</span>
                            GV: <span
                                class="font-semibold text-slate-700 dark:text-slate-300">{{ $class->advisor->user->name ?? 'Chưa phân công' }}</span>
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[14px]">groups</span>
                            Sĩ số: <span class="font-bold text-primary">{{ $class->students_count }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.classes.edit', $class->id) }}"
                    class="flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-sm text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium shadow-sm">
                    <span class="material-symbols-outlined !text-[15px]">handyman</span> Chỉnh sửa lớp
                </a>
            </div>
        </div>

        {{-- 2. TOOLBAR & TABLE --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    Danh sách Sinh viên
                    <span id="filtered-count" class="text-slate-500 text-sm font-normal">({{ $students->total() }})</span>
                </h3>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    {{-- SEARCH --}}
                    <div class="relative flex-1 md:flex-none">
                        <input type="text" id="live-search-input" value="{{ request('search') }}"
                            placeholder="Tìm nhanh (tên, MSSV)..."
                            class="pl-9 pr-4 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary w-full md:w-64 shadow-sm">
                        <span
                            class="material-symbols-outlined absolute left-2.5 top-2.5 text-slate-400 !text-[18px]">search</span>
                        <span id="search-spinner"
                            class="material-symbols-outlined absolute right-2.5 top-2.5 text-blue-500 !text-[18px] animate-spin hidden">progress_activity</span>
                    </div>

                    {{-- Nút Xóa Nhiều (Mặc định ẩn) --}}
                    <button type="button" id="btn-delete-selected"
                        class="hidden flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-sm hover:bg-red-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">delete</span> Xóa đã chọn
                    </button>

                    <button id="btn-open-create-modal"
                        class="flex items-center gap-2 px-3 py-2 bg-primary text-white text-sm font-medium rounded-sm hover:bg-primary/90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">add</span> Thêm SV
                    </button>

                    <a href="{{ route('admin.classes.export', $class->id) }}" id="btn-export-excel"
                        class="flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">docs</span> Excel
                    </a>

                    <button type="button" id="btn-send-selected-email"
                        class="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-sm hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined !text-[18px]">send</span> Gửi Mail
                    </button>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="overflow-x-auto relative">
                <div id="table-loading-overlay"
                    class="absolute inset-0 bg-white/50 z-10 hidden flex items-center justify-center">
                    <div class="bg-white p-2 rounded shadow border border-slate-200 flex items-center gap-2">
                        <span class="animate-spin material-symbols-outlined text-primary">progress_activity</span>
                        <span class="text-sm font-medium text-slate-700">Đang lọc...</span>
                    </div>
                </div>

                <table class="w-full text-left text-sm border-collapse">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3 w-10 text-center">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3 w-32">Mã SV</th>
                            <th class="px-6 py-3">Họ và Tên</th>
                            <th class="px-6 py-3 w-32">Ngày sinh</th>
                            <th class="px-6 py-3">Email</th>
                            <th class="px-6 py-3 w-32">Trạng thái</th>
                            <th class="px-6 py-3 w-24 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @include('admin.classes.partials.student_rows', ['students' => $students])
                    </tbody>
                </table>
            </div>

            <div id="pagination-links" class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL UNIVERSAL & LOADING --}}
    @include('admin.classes.partials.universal_confirm_modal')
    @include('admin.classes.partials.loading_modal')

    {{-- MODAL TẠO MỚI SINH VIÊN --}}
    <div id="createStudentModal" class="fixed inset-0 z-[130] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    <form id="formCreateStudent" action="{{ route('admin.students.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="class_id" value="{{ $class->id }}">
                        <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person_add</span> Thêm Sinh viên vào
                                lớp
                            </h3>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mã
                                            SV <span class="text-red-500">*</span></label>
                                        <input type="text" name="student_code" required
                                            class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary font-mono uppercase">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                            sinh</label>
                                        <input type="date" name="dob"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ và
                                        Tên <span class="text-red-500">*</span></label>
                                    <input type="text" name="fullname" required
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trạng
                                        thái</label>
                                    <select name="status"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                        <option value="studying">Đang học</option>
                                        <option value="reserved">Bảo lưu</option>
                                        <option value="dropped">Thôi học</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-800 px-4 py-3 flex justify-end gap-3 sm:px-6 border-t border-slate-100 dark:border-slate-700">
                            <button type="button"
                                onclick="document.getElementById('createStudentModal').classList.add('hidden')"
                                class="px-4 py-2 bg-white border border-slate-300 rounded-sm text-sm font-medium hover:bg-slate-50 text-slate-700">Hủy</button>
                            <button type="submit" id="btn-create-student-submit"
                                class="px-4 py-2 bg-primary text-white rounded-sm text-sm font-medium hover:bg-primary/90 flex items-center gap-2">
                                <span class="material-symbols-outlined !text-[16px]">save</span> Lưu lại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. BIẾN ---
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnDeleteSelected = document.getElementById('btn-delete-selected');
            const btnExportExcel = document.getElementById('btn-export-excel');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const filteredCountSpan = document.getElementById('filtered-count');
            const studentCountSpan = document.getElementById('student-count');

            // Modal Loading
            const loadingModal = document.getElementById('loadingModal');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const loadingTitle = document.getElementById('loading-modal-title');
            const loadingDesc = document.getElementById('loading-modal-desc');

            // Universal Modal
            const universalModal = document.getElementById('universalModal');
            const uniTitle = document.getElementById('uni-modal-title');
            const uniDesc = document.getElementById('uni-modal-desc');
            const uniBtnConfirm = document.getElementById('btn-uni-confirm');
            const uniBtnCancel = document.getElementById('btn-uni-cancel');
            const uniBtnText = document.getElementById('uni-modal-btn-text');
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');
            let pendingCallback = null;

            // --- 2. MODAL & HELPER ---
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
                uniBtnConfirm.innerText = btnText;
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

                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 ${style.btn}`;
                uniIcon.className = `material-symbols-outlined text-[24px] ${style.icon}`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center ${style.bg}`;

                uniBtnConfirm.classList.remove('hidden');
                uniBtnCancel.classList.remove('hidden');
                universalModal.classList.remove('hidden');
            }

            function showNotification(message) {
                uniTitle.innerText = "Thành công!";
                uniDesc.innerText = message;
                uniIcon.innerText = "check_circle";
                uniIconBg.className =
                    "flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-green-100 text-green-600";

                uniBtnConfirm.classList.add('hidden');
                uniBtnCancel.classList.add('hidden');
                universalModal.classList.remove('hidden');

                setTimeout(() => {
                    universalModal.classList.add('hidden');
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
                    universalModal.classList.add('hidden');
                    pendingCallback = null;
                });
            }

            function chunkArray(myArray, chunk_size) {
                var results = [];
                while (myArray.length) {
                    results.push(myArray.splice(0, chunk_size));
                }
                return results;
            }

            // --- 3. QUẢN LÝ TRẠNG THÁI CHECKBOX & NÚT ---
            // Tách hàm này ra ngoài để gọi được từ mọi nơi
            function toggleActionBtns() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                const selectedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
                const any = selectedCount > 0;

                // Logic nút Gửi Mail
                if (btnSendSelectedEmail) {
                    btnSendSelectedEmail.disabled = !any;
                    btnSendSelectedEmail.classList.toggle('opacity-50', !any);
                    btnSendSelectedEmail.classList.toggle('cursor-not-allowed', !any);
                }

                // Logic nút Xóa Nhiều
                if (btnDeleteSelected) {
                    if (any) {
                        btnDeleteSelected.classList.remove('hidden');
                        btnDeleteSelected.innerHTML =
                            `<span class="material-symbols-outlined !text-[18px]">delete</span> Xóa (${selectedCount})`;
                    } else {
                        btnDeleteSelected.classList.add('hidden');
                    }
                }
            }

            // --- 4. INIT TABLE EVENTS ---
            function initTableEvents() {
                const checkboxes = document.querySelectorAll('.student-checkbox');

                // Gán sự kiện cho checkbox select all
                if (selectAll) {
                    // Remove old event listener to avoid duplicates if re-init
                    selectAll.onclick = null;
                    selectAll.checked = false; // Reset khi load lại bảng

                    selectAll.onclick = function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        toggleActionBtns();
                    };
                }

                // Gán sự kiện cho từng checkbox
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', toggleActionBtns);
                });

                // Reset trạng thái ban đầu
                toggleActionBtns();

                // Gán sự kiện Delete (1 dòng)
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        const code = this.getAttribute('data-code');
                        const url = form.action;

                        showConfirm({
                            title: 'Xóa Sinh Viên?',
                            message: `Bạn có chắc muốn xóa sinh viên ${code}? Hành động này sẽ chuyển sinh viên vào thùng rác.`,
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
                                        showNotification(data.message);

                                        // Xóa dòng khỏi bảng
                                        const row = btn.closest('tr');
                                        if (row) row.remove();

                                        // Cập nhật số lượng
                                        if (filteredCountSpan) {
                                            let count = parseInt(filteredCountSpan
                                                    .innerText.replace(/[()]/g, '')) ||
                                                0;
                                            if (count > 0) filteredCountSpan.innerText =
                                                `(${count - 1})`;
                                        }

                                        // [QUAN TRỌNG] Cập nhật lại trạng thái nút xóa chung
                                        toggleActionBtns();

                                    } else {
                                        alert("Lỗi: " + data.message);
                                    }
                                } catch (e) {
                                    console.error(e);
                                    alert("Có lỗi xảy ra khi xóa.");
                                }
                            }
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

                // Edit (Giữ nguyên)
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
            }
            initTableEvents();

            // --- 5. XỬ LÝ NÚT XÓA NHIỀU ---
            if (btnDeleteSelected) {
                btnDeleteSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox'))
                        .filter(cb => cb.checked).map(cb => cb.value);

                    if (ids.length === 0) return;

                    showConfirm({
                        title: 'Xóa ' + ids.length + ' Sinh Viên?',
                        message: 'Các sinh viên đã chọn sẽ bị chuyển vào thùng rác. Bạn có chắc chắn?',
                        btnText: 'Xóa tất cả',
                        btnColor: 'red',
                        icon: 'delete_forever',
                        callback: async () => {
                            loadingModal.classList.remove('hidden');
                            if (progressContainer) progressContainer.classList.add('hidden');
                            if (loadingTitle) loadingTitle.innerText = "Đang xóa dữ liệu...";
                            if (loadingDesc) loadingDesc.innerText = "Vui lòng chờ...";

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
                                    showNotification(data.message);
                                    // Xóa các dòng đã chọn khỏi bảng
                                    ids.forEach(id => {
                                        const checkbox = document.querySelector(
                                            `.student-checkbox[value="${id}"]`);
                                        if (checkbox) {
                                            const row = checkbox.closest('tr');
                                            if (row) row.remove();
                                        }
                                    });
                                    // Cập nhật số lượng hiển thị
                                    if (filteredCountSpan) {
                                        let current = parseInt(filteredCountSpan.innerText
                                            .replace(/[()]/g, '')) || 0;
                                        let newCount = Math.max(0, current - ids.length);
                                        filteredCountSpan.innerText = `(${newCount})`;
                                    }

                                    // Reset toolbar & Checkbox select all
                                    if (selectAll) selectAll.checked = false;

                                    // [QUAN TRỌNG] Gọi lại hàm cập nhật nút để ẩn nút xóa
                                    toggleActionBtns();

                                } else {
                                    alert("Lỗi: " + data.message);
                                }
                            } catch (e) {
                                console.error(e);
                                loadingModal.classList.add('hidden');
                                alert("Có lỗi xảy ra khi xóa nhiều.");
                            }
                        }
                    });
                });
            }

            // --- 6. CREATE STUDENT AJAX ---
            const formCreateStudent = document.getElementById('formCreateStudent');
            const btnCreateStudent = document.getElementById('btn-create-student-submit');
            const modalCreateStudent = document.getElementById('createStudentModal');
            const btnOpenCreateModal = document.getElementById('btn-open-create-modal');

            if (btnOpenCreateModal) {
                btnOpenCreateModal.addEventListener('click', function() {
                    modalCreateStudent.classList.remove('hidden');
                });
            }

            if (formCreateStudent) {
                formCreateStudent.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    if (!this.checkValidity()) {
                        this.reportValidity();
                        return;
                    }

                    loadingModal.classList.remove('hidden');
                    if (progressContainer) progressContainer.classList.add('hidden');
                    loadingTitle.innerText = "Đang lưu dữ liệu...";
                    loadingDesc.innerText = "Vui lòng chờ giây lát.";
                    btnCreateStudent.disabled = true;

                    try {
                        const formData = new FormData(this);
                        const response = await fetch(this.action, {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });
                        const contentType = response.headers.get("content-type");
                        if (!contentType || !contentType.includes("application/json")) {
                            throw new Error("Lỗi Server");
                        }
                        const data = await response.json();
                        if (!data.success) throw new Error(data.message);

                        loadingModal.classList.add('hidden');
                        modalCreateStudent.classList.add('hidden');
                        btnCreateStudent.disabled = false;
                        this.reset();

                        const template = document.createElement('template');
                        template.innerHTML = data.html.trim();
                        const newRow = template.content.firstChild;
                        newRow.classList.add('bg-yellow-100', 'dark:bg-yellow-900/30',
                            'transition-colors', 'duration-1000');
                        tableBody.insertBefore(newRow, tableBody.firstChild);

                        if (filteredCountSpan) {
                            let count = parseInt(filteredCountSpan.innerText.replace(/[()]/g, '')) || 0;
                            filteredCountSpan.innerText = `(${count + 1})`;
                        }

                        // Re-bind events cho dòng mới
                        initTableEvents();

                        setTimeout(() => {
                            newRow.classList.remove('bg-yellow-100', 'dark:bg-yellow-900/30');
                        }, 3000);
                        showNotification("Thêm sinh viên thành công!");

                    } catch (error) {
                        loadingModal.classList.add('hidden');
                        btnCreateStudent.disabled = false;
                        alert("Lỗi: " + error.message);
                    }
                });
            }

            // --- 7. LIVE SEARCH & EXPORT ---
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
                                if (filteredCountSpan) filteredCountSpan.innerText =
                                    `(${data.total_found})`;
                                initTableEvents();
                            })
                            .finally(() => {
                                searchSpinner.classList.add('hidden');
                                tableOverlay.classList.add('hidden');
                            });
                    }, 400);
                });
            }

            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    showConfirm({
                        title: 'Xuất Excel',
                        message: 'Tải xuống danh sách sinh viên lớp này?',
                        btnText: 'Tải xuống',
                        btnColor: 'green',
                        icon: 'docs',
                        callback: () => window.location.href = url
                    });
                });
            }

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
                        progressText.innerText = `Đã gửi ${processed}/${total} (${percent}%)`;
                    } catch (error) {
                        console.error(error);
                        processed += batch.length;
                    }
                }
                setTimeout(() => {
                    loadingModal.classList.add('hidden');
                    progressContainer.classList.add('hidden');
                    showNotification(`Đã hoàn tất gửi ${processed} email!`);
                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                    if (selectAll) selectAll.checked = false;
                    toggleActionBtns(); // Update toolbar state
                }, 500);
            }
        });
    </script>
@endsection
