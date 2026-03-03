@extends('layouts.admin')
@section('title', 'Chi tiết Lớp ' . $class->code)

@section('content')
    <div class="w-full px-4 py-6" x-data="{ showImportModal: false, showCreateModal: false }" @close-create-modal.window="showCreateModal = false">

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

                    {{-- Nút Khôi Phục Nhiều (Mới thêm) --}}
                    <button type="button" id="btn-restore-selected"
                        class="hidden flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-sm hover:bg-blue-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">history</span> Khôi phục
                    </button>

                    {{-- Nút Xóa (Ẩn) Nhiều --}}
                    <button type="button" id="btn-delete-selected"
                        class="hidden flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm font-medium rounded-sm hover:bg-red-700 transition-colors shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">visibility_off</span> Ẩn đã chọn
                    </button>

                    <button @click="showCreateModal = true"
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

        {{-- ================= MODAL TẠO MỚI SINH VIÊN (GIỮ NGUYÊN) ================= --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-2xl rounded-lg shadow-xl overflow-hidden"
                @click.away="showCreateModal = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">person_add</span> Thêm Sinh viên mới
                    </h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-red-500"><span
                            class="material-symbols-outlined">close</span></button>
                </div>

                <form id="formCreateStudent" action="{{ route('admin.students.store') }}" method="POST" class="p-6">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->id }}">
                    <div id="create-student-error"
                        class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"></div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Mã Sinh
                                    Viên <span class="text-red-500">*</span></label>
                                <input type="text" name="student_code" id="create_student_code" required
                                    placeholder="VD: 20110001"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm font-mono uppercase focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Ngày
                                    sinh</label>
                                <input type="date" name="dob"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ và
                                Tên <span class="text-red-500">*</span></label>
                            <input type="text" name="fullname" id="create_fullname" required
                                placeholder="VD: Nguyễn Văn A"
                                class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Email Hệ thống</label>
                            <div class="relative">
                                <input type="email" name="email" id="create_email" placeholder="Để trống để tự tạo"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary pr-10">
                                <span
                                    class="absolute right-3 top-2 text-slate-400 material-symbols-outlined !text-[18px]">mail</span>
                            </div>
                            <p class="text-[11px] text-blue-500 mt-1 italic">Hệ thống sẽ tự động tạo email dựa vào tên và
                                mã SV.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Trạng
                                thái <span class="text-red-500">*</span></label>
                            <select name="status" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                <option value="studying" selected>Đang học</option>
                                <option value="reserved">Bảo lưu</option>
                                <option value="dropped">Thôi học</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 flex justify-end gap-3 border-t border-slate-100 dark:border-slate-700">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">Hủy
                            bỏ</button>
                        <button type="submit" id="btn-submit-create"
                            class="px-5 py-2 bg-primary text-white rounded-sm hover:bg-primary/90 flex items-center gap-2 text-sm font-medium shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined !text-[16px]">save</span> Thêm Sinh Viên
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    {{-- MODAL UNIVERSAL & LOADING & EDIT --}}
    @include('admin.classes.partials.universal_confirm_modal')
    @include('admin.classes.partials.loading_modal')

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
                        <input type="hidden" id="edit_student_code_hidden">

                        <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-4 flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary">person_edit</span> Cập nhật Sinh viên
                            </h3>

                            <div id="edit-student-error"
                                class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"></div>

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
                                    <p class="text-[11px] text-blue-500 mt-1 italic">Hệ thống sẽ tự động cập nhật email dựa
                                        vào tên và mã SV nếu bạn thay đổi tên.</p>
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
                            <button type="submit" id="btn-submit-edit"
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
            // --- 1. BIẾN TOÀN CỤC CHUNG ---
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnDeleteSelected = document.getElementById('btn-delete-selected');
            const btnRestoreSelected = document.getElementById('btn-restore-selected'); // Mới thêm
            const btnExportExcel = document.getElementById('btn-export-excel');
            const searchInput = document.getElementById('live-search-input');
            const searchSpinner = document.getElementById('search-spinner');
            const tableOverlay = document.getElementById('table-loading-overlay');
            const tableBody = document.getElementById('students-table-body');
            const paginationLinks = document.getElementById('pagination-links');
            const filteredCountSpan = document.getElementById('filtered-count');

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
            const uniIcon = document.getElementById('uni-modal-icon');
            const uniIconBg = document.getElementById('uni-modal-icon-bg');
            let pendingCallback = null;

            // --- 2. HÀM TẠO EMAIL TỰ ĐỘNG ---
            function generateEmail(fullnameStr, codeStr) {
                if (!fullnameStr || !codeStr) return '';
                let str = fullnameStr.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                str = str.replace(/đ/g, "d").replace(/Đ/g, "D");
                str = str.trim().toLowerCase();
                const parts = str.split(/\s+/);
                const lastName = parts.pop();
                if (lastName && codeStr) {
                    return `${lastName}${codeStr.toLowerCase()}@vnkgu.edu.vn`;
                }
                return '';
            }

            // Tự động tạo Email cho Form THÊM MỚI
            const createFullname = document.getElementById('create_fullname');
            const createCode = document.getElementById('create_student_code');
            const createEmail = document.getElementById('create_email');

            function handleCreateEmailGen() {
                if (createFullname && createCode && createEmail) {
                    const email = generateEmail(createFullname.value, createCode.value);
                    if (email) createEmail.value = email;
                }
            }
            if (createFullname && createCode) {
                createFullname.addEventListener('blur', handleCreateEmailGen);
                createCode.addEventListener('blur', handleCreateEmailGen);
            }

            // Tự động tạo Email cho Form CẬP NHẬT
            const editFullname = document.getElementById('edit_fullname');
            const editCodeHidden = document.getElementById('edit_student_code_hidden');
            const editEmail = document.getElementById('edit_email');
            if (editFullname && editCodeHidden && editEmail) {
                editFullname.addEventListener('input', function() {
                    const email = generateEmail(editFullname.value, editCodeHidden.value);
                    if (email) editEmail.value = email;
                });
            }

            // --- 3. AJAX CREATE ---
            const formCreateStudent = document.getElementById('formCreateStudent');
            const btnCreateStudent = document.getElementById('btn-submit-create');
            const errorDivCreate = document.getElementById('create-student-error');

            if (formCreateStudent) {
                formCreateStudent.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    errorDivCreate.classList.add('hidden');
                    const originalBtnHtml = btnCreateStudent.innerHTML;
                    btnCreateStudent.disabled = true;
                    btnCreateStudent.innerHTML =
                        '<span class="material-symbols-outlined !text-[18px] animate-spin">progress_activity</span> Đang xử lý...';

                    try {
                        const formData = new FormData(this);
                        const response = await fetch(this.action, {
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
                            let errorHtml =
                                '<strong>Lỗi nhập liệu:</strong><ul class="list-disc pl-5 mt-1">';
                            for (const [key, value] of Object.entries(data.errors)) {
                                errorHtml += `<li>${value[0]}</li>`;
                            }
                            errorHtml += '</ul>';
                            errorDivCreate.innerHTML = errorHtml;
                            errorDivCreate.classList.remove('hidden');
                            showToast('warning', 'Vui lòng kiểm tra lại thông tin nhập vào!');
                        } else if (!data.success) {
                            throw new Error(data.message || 'Lỗi server');
                        } else {
                            window.dispatchEvent(new CustomEvent('close-create-modal'));
                            this.reset();
                            showToast('success', 'Thêm sinh viên thành công!');
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        errorDivCreate.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                        errorDivCreate.classList.remove('hidden');
                        showToast('error', error.message);
                    } finally {
                        btnCreateStudent.disabled = false;
                        btnCreateStudent.innerHTML = originalBtnHtml;
                    }
                });
            }

            // --- 4. AJAX UPDATE ---
            const formEditStudent = document.getElementById('formEditStudent');
            const btnEditStudent = document.getElementById('btn-submit-edit');
            const errorDivEdit = document.getElementById('edit-student-error');

            if (formEditStudent) {
                formEditStudent.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    errorDivEdit.classList.add('hidden');
                    const originalBtnHtml = btnEditStudent.innerHTML;
                    btnEditStudent.disabled = true;
                    btnEditStudent.innerHTML =
                        '<span class="material-symbols-outlined !text-[18px] animate-spin">progress_activity</span> Đang xử lý...';

                    try {
                        const formData = new FormData(this);
                        const response = await fetch(this.action, {
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
                            let errorHtml =
                                '<strong>Lỗi nhập liệu:</strong><ul class="list-disc pl-5 mt-1">';
                            for (const [key, value] of Object.entries(data.errors)) {
                                errorHtml += `<li>${value[0]}</li>`;
                            }
                            errorHtml += '</ul>';
                            errorDivEdit.innerHTML = errorHtml;
                            errorDivEdit.classList.remove('hidden');
                            showToast('warning', 'Vui lòng kiểm tra lại thông tin nhập vào!');
                        } else if (!data.success) {
                            throw new Error(data.message || 'Lỗi server');
                        } else {
                            document.getElementById('editStudentModal').classList.add('hidden');
                            showToast('success', 'Cập nhật thông tin thành công!');
                            setTimeout(() => window.location.reload(), 1000);
                        }
                    } catch (error) {
                        errorDivEdit.innerHTML = `<strong>Lỗi:</strong> ${error.message}`;
                        errorDivEdit.classList.remove('hidden');
                        showToast('error', error.message);
                    } finally {
                        btnEditStudent.disabled = false;
                        btnEditStudent.innerHTML = originalBtnHtml;
                    }
                });
            }

            // --- 5. MODAL XÁC NHẬN ---
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
                    }
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

            // --- 6. QUẢN LÝ CHECKBOX & ACTION BTNS ---
            function toggleActionBtns() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
                const selectedCount = checkedBoxes.length;

                let hasActive = false;
                let hasTrashed = false;

                checkedBoxes.forEach(cb => {
                    if (cb.dataset.trashed === 'true') hasTrashed = true;
                    else hasActive = true;
                });

                // Reset buttons
                if (btnDeleteSelected) btnDeleteSelected.classList.add('hidden');
                if (btnRestoreSelected) btnRestoreSelected.classList.add('hidden');
                if (btnSendSelectedEmail) {
                    btnSendSelectedEmail.disabled = true;
                    btnSendSelectedEmail.classList.add('opacity-50', 'cursor-not-allowed');
                }

                if (selectedCount > 0) {
                    if (hasActive && hasTrashed) {
                        // Không hiện gì nếu chọn lẫn lộn
                    } else if (hasTrashed) {
                        if (btnRestoreSelected) {
                            btnRestoreSelected.classList.remove('hidden');
                            btnRestoreSelected.innerHTML =
                                `<span class="material-symbols-outlined !text-[18px]">history</span> Khôi phục (${selectedCount})`;
                        }
                    } else {
                        if (btnDeleteSelected) {
                            btnDeleteSelected.classList.remove('hidden');
                            btnDeleteSelected.innerHTML =
                                `<span class="material-symbols-outlined !text-[18px]">visibility_off</span> Ẩn (${selectedCount})`;
                        }
                        if (btnSendSelectedEmail) {
                            btnSendSelectedEmail.disabled = false;
                            btnSendSelectedEmail.classList.remove('opacity-50', 'cursor-not-allowed');
                        }
                    }
                }
            }

            function initTableEvents() {
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

                // NÚT ẨN 1 NGƯỜI
                document.querySelectorAll('.btn-delete-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const form = this.closest('form');
                        const code = this.getAttribute('data-code');
                        const url = form.action;
                        showConfirm({
                            title: 'Ẩn Sinh Viên?',
                            message: `Bạn có chắc muốn ẩn sinh viên ${code}? Sinh viên này sẽ bị vô hiệu hóa nhưng không mất dữ liệu.`,
                            btnText: 'Ẩn ngay',
                            btnColor: 'red',
                            icon: 'visibility_off',
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
                                        showToast('success', data.message);
                                        setTimeout(() => window.location.reload(), 500);
                                    } else {
                                        showToast('error', data.message);
                                    }
                                } catch (e) {
                                    showToast('error', 'Có lỗi xảy ra.');
                                }
                            }
                        });
                    });
                });

                // NÚT KHÔI PHỤC 1 NGƯỜI
                document.querySelectorAll('.btn-restore-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const url = this.getAttribute('data-url');
                        showConfirm({
                            title: 'Khôi phục Sinh Viên?',
                            message: 'Bạn muốn kích hoạt lại sinh viên này?',
                            btnText: 'Khôi phục',
                            btnColor: 'blue',
                            icon: 'history',
                            callback: async () => {
                                try {
                                    const response = await fetch(url, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json'
                                        }
                                    });
                                    const data = await response.json();
                                    if (data.success) {
                                        showToast('success', data.message);
                                        setTimeout(() => window.location.reload(), 500);
                                    } else {
                                        showToast('error', data.message);
                                    }
                                } catch (e) {
                                    showToast('error', 'Có lỗi xảy ra khi khôi phục.');
                                }
                            }
                        });
                    });
                });

                // NÚT SỬA 1 NGƯỜI
                document.querySelectorAll('.btn-edit-student').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        const code = this.getAttribute('data-code');
                        const formEdit = document.getElementById('formEditStudent');
                        const editModal = document.getElementById('editStudentModal');
                        const errorDiv = document.getElementById('edit-student-error');

                        if (errorDiv) errorDiv.classList.add('hidden');
                        formEdit.action = `/admin/students/${id}`;

                        let rawDob = this.getAttribute('data-dob') || '';
                        if (rawDob && rawDob.includes(' ')) {
                            rawDob = rawDob.split(' ')[0];
                        }

                        document.getElementById('edit_student_code_hidden').value = code;
                        document.getElementById('edit_fullname').value = this.getAttribute(
                            'data-fullname');
                        document.getElementById('edit_email').value = this.getAttribute(
                            'data-email');
                        document.getElementById('edit_dob').value = rawDob;
                        document.getElementById('edit_status').value = this.getAttribute(
                            'data-status');

                        editModal.classList.remove('hidden');
                    });
                });

                // NÚT GỬI MAIL 1 NGƯỜI
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
            initTableEvents();

            // --- 7. BULK ACTIONS ---

            // XÓA (ẨN) NHIỀU
            if (btnDeleteSelected) {
                btnDeleteSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb =>
                        cb.value);
                    if (ids.length === 0) return;
                    showConfirm({
                        title: 'Ẩn ' + ids.length + ' Sinh Viên?',
                        message: 'Các sinh viên đã chọn sẽ bị ẩn (vô hiệu hóa). Bạn có chắc chắn?',
                        btnText: 'Ẩn tất cả',
                        btnColor: 'red',
                        icon: 'visibility_off',
                        callback: async () => {
                            loadingModal.classList.remove('hidden');
                            if (progressContainer) progressContainer.classList.add('hidden');
                            if (loadingTitle) loadingTitle.innerText = "Đang xử lý...";
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
                                    showToast('success', data.message);
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    showToast('error', data.message);
                                }
                            } catch (e) {
                                loadingModal.classList.add('hidden');
                                showToast('error', 'Có lỗi xảy ra.');
                            }
                        }
                    });
                });
            }

            // KHÔI PHỤC NHIỀU
            if (btnRestoreSelected) {
                btnRestoreSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb =>
                        cb.value);
                    if (ids.length === 0) return;

                    showConfirm({
                        title: 'Khôi phục ' + ids.length + ' Sinh Viên?',
                        message: 'Các sinh viên đã chọn sẽ được kích hoạt lại.',
                        btnText: 'Khôi phục tất cả',
                        btnColor: 'blue',
                        icon: 'history',
                        callback: async () => {
                            loadingModal.classList.remove('hidden');
                            if (loadingTitle) loadingTitle.innerText = "Đang khôi phục...";
                            if (progressContainer) progressContainer.classList.add('hidden');

                            try {
                                const response = await fetch(
                                    '{{ route('admin.students.bulk_restore') }}', {
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
                                    showToast('success', data.message);
                                    setTimeout(() => window.location.reload(), 1000);
                                } else {
                                    showToast('error', data.message);
                                }
                            } catch (e) {
                                loadingModal.classList.add('hidden');
                                showToast('error', 'Lỗi hệ thống khi khôi phục.');
                            }
                        }
                    });
                });
            }

            // --- 8. SEARCH, EXPORT, EMAIL BATCH ---
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
                    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb =>
                        cb.value);
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

            // Hàm gửi email theo lô
            function chunkArray(myArray, chunk_size) {
                var results = [];
                while (myArray.length) {
                    results.push(myArray.splice(0, chunk_size));
                }
                return results;
            }

            async function sendEmailsAjax(allIds) {
                loadingModal.classList.remove('hidden');
                if (progressContainer) progressContainer.classList.remove('hidden');
                const total = allIds.length;
                let processed = 0;
                const batches = chunkArray([...allIds], 3);
                if (loadingTitle) loadingTitle.innerText = "Đang gửi Email...";
                if (progressBar) progressBar.style.width = "0%";
                if (progressText) progressText.innerText = `Đã gửi 0/${total}`;

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
                        if (progressBar) progressBar.style.width = `${percent}%`;
                        if (progressText) progressText.innerText = `Đã gửi ${processed}/${total} (${percent}%)`;
                    } catch (error) {
                        console.error(error);
                        processed += batch.length;
                    }
                }
                setTimeout(() => {
                    loadingModal.classList.add('hidden');
                    if (progressContainer) progressContainer.classList.add('hidden');
                    showToast('success', `Đã hoàn tất gửi ${processed} email!`);
                    document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
                    if (selectAll) selectAll.checked = false;
                    toggleActionBtns();
                }, 500);
            }
        });
    </script>
@endsection
