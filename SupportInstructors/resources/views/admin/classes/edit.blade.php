@extends('layouts.admin')
@section('title', 'Cập nhật Lớp học')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="w-full">
        <div class="mb-6">
            <x-page-header title="Cập nhật Lớp học" description="Chỉnh sửa thông tin lớp {{ $class->code }}"
                :routePrefix="$routePrefix" :breadcrumbs="[
                    ['label' => 'Lớp học', 'url' => route($routePrefix . 'classes.index')],
                    ['label' => 'Cập nhật'],
                ]" />
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm mb-6">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">edit_square</span> Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route($routePrefix . 'classes.update', $class->id) }}" method="POST"
                enctype="multipart/form-data" class="p-6" id="editClassForm" novalidate>
                @csrf
                @method('PUT')

                <input type="hidden" name="send_email" id="send_email_import_input" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $class->code) }}" required
                            {{ Auth::user()->role_id == 2 ? 'readonly' : '' }}
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm {{ Auth::user()->role_id == 2 ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="code"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required {{ Auth::user()->role_id == 2 ? 'readonly' : '' }}
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm {{ Auth::user()->role_id == 2 ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="academic_year"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                            {{ Auth::user()->role_id == 2 ? 'readonly' : '' }}
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm {{ Auth::user()->role_id == 2 ? 'bg-slate-100 cursor-not-allowed text-slate-500' : '' }}">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="name"></p>
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Đơn vị quản
                            lý</label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-slate-500 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa CNTT' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập <span class="text-red-500">*</span>
                        </label>

                        @if (Auth::user()->role_id == 1)
                            <select name="advisor_id" id="select-advisor" required
                                class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                                <option value="">-- Chọn Giảng viên --</option>
                                @foreach ($lecturers as $lec)
                                    <option value="{{ $lec->id }}"
                                        {{ old('advisor_id', $class->advisor_id) == $lec->id ? 'selected' : '' }}>
                                        {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        @else
                            {{-- Giảng viên chỉ được xem tên, không được đổi --}}
                            <div
                                class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-slate-500 text-sm font-medium cursor-not-allowed">
                                {{ $class->advisor->user->name ?? 'Chưa xác định' }}
                            </div>
                            <input type="hidden" name="advisor_id" value="{{ $class->advisor_id }}">
                        @endif
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="advisor_id"></p>
                    </div>

                    {{-- Cán bộ lớp (Ai cũng được sửa) --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lớp trưởng
                        </label>
                        <select name="monitor_id" id="select-monitor"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                            <option value="">-- Chọn Lớp trưởng --</option>
                            @foreach ($studentCandidates as $stu)
                                <option value="{{ $stu->id }}"
                                    {{ old('monitor_id', $class->monitor_id) == $stu->id ? 'selected' : '' }}>
                                    {{ $stu->student_code }} - {{ $stu->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Thư ký / Lớp phó
                        </label>
                        <select name="secretary_id" id="select-secretary"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                            <option value="">-- Chọn Thư ký --</option>
                            @foreach ($studentCandidates as $stu)
                                <option value="{{ $stu->id }}"
                                    {{ old('secretary_id', $class->secretary_id) == $stu->id ? 'selected' : '' }}>
                                    {{ $stu->student_code }} - {{ $stu->fullname }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Thêm Sinh Viên --}}
                    <div class="md:col-span-2 mt-2">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-3 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 !text-[20px]">group_add</span>
                                Thêm Sinh viên Mới vào Lớp (Import Excel hoặc Nhập tay)
                            </label>

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

                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-bold text-slate-500 uppercase">Hoặc tải file lên (xlsx,
                                    csv):</span>
                            </div>
                            <input type="file" name="student_file_temp" id="student_file_input" accept=".xlsx, .csv"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

                            <p class="text-red-500 text-xs mt-1 error-msg" data-field="student_file"></p>
                            <p id="upload-error" class="text-red-500 text-xs mt-2 hidden font-bold"></p>

                            <div id="new-students-preview" class="mt-4"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route($routePrefix . 'classes.index') }}"
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

                    <a href="{{ route($routePrefix . 'classes.export', $class->id) }}" id="btn-export-excel"
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
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                        <tr>
                            @if (Auth::user()->role_id == 1)
                                <th class="px-6 py-3 w-10 text-center">
                                    <input type="checkbox" id="select-all"
                                        class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                                </th>
                            @endif

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

    @include('admin.classes.partials.loading_modal')
    @include('admin.classes.partials.universal_confirm_modal')

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
                                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">Họ và
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
            // --- 1. BIẾN TOÀN CỤC CHUNG ---
            const selectAll = document.getElementById('select-all');
            const btnSendSelectedEmail = document.getElementById('btn-send-selected-email');
            const btnDeleteSelected = document.getElementById('btn-delete-selected');
            const btnRestoreSelected = document.getElementById('btn-restore-selected');
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

                // --- CHỈ ADMIN ĐƯỢC PHÉP RENDER JS CHO XÓA/KHÔI PHỤC TỪNG SINH VIÊN ---
                @if (Auth::user()->role_id == 1)
                    document.querySelectorAll('.btn-delete-student').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const code = this.getAttribute('data-code');
                            const url = this.closest('form').action;
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
                                            setTimeout(() => window.location.reload(),
                                                500);
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
                                            setTimeout(() => window.location.reload(),
                                                500);
                                        } else {
                                            showToast('error', data.message);
                                        }
                                    } catch (e) {
                                        showToast('error',
                                            'Có lỗi xảy ra khi khôi phục.');
                                    }
                                }
                            });
                        });
                    });
                @endif
            }
            initTableEvents();

            // --- 7. BULK ACTIONS (CHỈ RENDER JS NẾU LÀ ADMIN) ---
            @if (Auth::user()->role_id == 1)
                if (btnDeleteSelected) {
                    btnDeleteSelected.addEventListener('click', function() {
                        const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(
                            cb =>
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
                                if (progressContainer) progressContainer.classList.add(
                                    'hidden');
                                if (loadingTitle) loadingTitle.innerText = "Đang xử lý...";
                                try {
                                    const response = await fetch(
                                        "{{ route('admin.students.bulk_destroy') }}", {
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

                if (btnRestoreSelected) {
                    btnRestoreSelected.addEventListener('click', function() {
                        const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(
                            cb =>
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
                                if (progressContainer) progressContainer.classList.add(
                                    'hidden');

                                try {
                                    const response = await fetch(
                                        "{{ route('admin.students.bulk_restore') }}", {
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
            @endif

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
                        await fetch('{{ route($routePrefix . 'classes.send_emails') }}', {
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
