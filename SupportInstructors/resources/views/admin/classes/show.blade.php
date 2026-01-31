@extends('layouts.admin')
@section('title', 'Chi tiết Lớp ' . $class->code)

@section('content')
    <div class="w-full px-4 py-6">

        {{-- 1. HEADER: THÔNG TIN LỚP HỌC --}}
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
                            {{-- Sĩ số cố định (Dùng students_count từ Controller) --}}
                            Sĩ số: <span class="font-bold text-primary">{{ $class->students_count }}</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.classes.edit', $class->id) }}"
                    class="flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-sm text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium shadow-sm">
                    <span class="material-symbols-outlined !text-[15px]">settings</span> Cài đặt Lớp
                </a>
            </div>
        </div>

        {{-- 2. TOOLBAR & DANH SÁCH SINH VIÊN --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    Danh sách Sinh viên
                    {{-- Số lượng thay đổi theo bộ lọc --}}
                    <span id="filtered-count" class="text-slate-500 text-sm font-normal">({{ $students->total() }})</span>
                </h3>

                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    {{-- SEARCH LIVE --}}
                    <div class="relative flex-1 md:flex-none">
                        <input type="text" id="live-search-input" value="{{ request('search') }}"
                            placeholder="Tìm nhanh (tên, MSSV)..."
                            class="pl-9 pr-4 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary w-full md:w-64 shadow-sm">
                        <span
                            class="material-symbols-outlined absolute left-2.5 top-2.5 text-slate-400 !text-[18px]">search</span>
                        <span id="search-spinner"
                            class="material-symbols-outlined absolute right-2.5 top-2.5 text-blue-500 !text-[18px] animate-spin hidden">progress_activity</span>
                    </div>

                    {{-- Nút Thêm Mới (Modal) --}}
                    <button onclick="document.getElementById('createStudentModal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-3 py-2 bg-primary text-white text-sm font-medium rounded-sm hover:bg-primary/90 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">add</span> Thêm SV
                    </button>

                    {{-- Export Excel --}}
                    <a href="{{ route('admin.classes.export', $class->id) }}" id="btn-export-excel"
                        class="flex items-center gap-2 px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">download</span> Excel
                    </a>

                    {{-- Gửi Mail Hàng Loạt --}}
                    <button type="button" id="btn-send-selected-email"
                        class="flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-sm hover:bg-blue-700 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="material-symbols-outlined !text-[18px]">send</span> Gửi Mail
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto relative">
                {{-- Loading Overlay --}}
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

            {{-- Pagination --}}
            <div id="pagination-links" class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    {{-- MODAL UNIVERSAL & LOADING --}}
    @include('admin.classes.partials.universal_confirm_modal')
    @include('admin.classes.partials.loading_modal')

    {{-- MODAL TẠO MỚI SINH VIÊN (Dành riêng cho lớp này) --}}
    <div id="createStudentModal" class="fixed inset-0 z-[130] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    <form action="{{ route('admin.students.store') }}" method="POST">
                        @csrf
                        {{-- Input Hidden Class ID cố định cho lớp này --}}
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
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div
                            class="bg-slate-50 dark:bg-slate-800 px-4 py-3 flex justify-end gap-3 sm:px-6 border-t border-slate-100 dark:border-slate-700">
                            <button type="button"
                                onclick="document.getElementById('createStudentModal').classList.add('hidden')"
                                class="px-4 py-2 bg-white border border-slate-300 rounded-sm text-sm font-medium hover:bg-slate-50 text-slate-700">Hủy</button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary text-white rounded-sm text-sm font-medium hover:bg-primary/90 flex items-center gap-2">
                                <span class="material-symbols-outlined !text-[16px]">save</span> Lưu lại
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL SỬA SINH VIÊN (Dùng chung) --}}
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
                                        thái</label>
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
            // 1. VARIABLE DECLARATION
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnExportExcel = document.getElementById('btn-export-excel');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const filteredCountSpan = document.getElementById('filtered-count');
            const loadingModal = document.getElementById('loadingModal');

            // 2. UNIVERSAL MODAL LOGIC
            const universalModal = document.getElementById('universalModal');
            const uniTitle = document.getElementById('uni-modal-title');
            const uniDesc = document.getElementById('uni-modal-desc');
            const uniBtnConfirm = document.getElementById('btn-uni-confirm');
            const uniBtnCancel = document.getElementById('btn-uni-cancel');
            const uniBtnText = document.getElementById('uni-modal-btn-text');
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');
            let pendingCallback = null;

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

            // 3. TABLE EVENTS (INIT)
            function initTableEvents() {
                // Checkbox Logic
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

                // Delete Button
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        const code = this.getAttribute('data-code');
                        showConfirm({
                            title: 'Xóa Sinh Viên?',
                            message: `Bạn có chắc muốn xóa sinh viên ${code}? Hành động này không thể hoàn tác.`,
                            btnText: 'Xóa ngay',
                            btnColor: 'red',
                            icon: 'warning',
                            callback: () => form.submit()
                        });
                    });
                });

                // Single Email Button
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

                // Edit Button
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
            }
            initTableEvents();

            // 4. LIVE SEARCH LOGIC
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
                                initTableEvents(); // Re-bind events for new rows
                            })
                            .finally(() => {
                                searchSpinner.classList.add('hidden');
                                tableOverlay.classList.add('hidden');
                            });
                    }, 400);
                });
            }

            // 5. OTHER ACTIONS
            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    showConfirm({
                        title: 'Xuất Excel',
                        message: 'Tải xuống danh sách sinh viên lớp này?',
                        btnText: 'Tải xuống',
                        btnColor: 'green',
                        icon: 'download',
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
                }).then(res => res.json()).then(data => {
                    loadingModal.classList.add('hidden');
                    alert(data.message);
                }).catch(err => {
                    loadingModal.classList.add('hidden');
                    alert('Lỗi gửi mail.');
                });
            }
        });
    </script>
@endsection
