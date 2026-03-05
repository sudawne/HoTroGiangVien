@extends('layouts.admin')
@section('title', 'Cập nhật Lớp học')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.default.min.css" rel="stylesheet">
@endsection

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
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="code"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="academic_year"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="name"></p>
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
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="advisor_id"></p>
                    </div>

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
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                        <tr>
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
            var commonConfig = {
                create: false,
                sortField: {
                    field: "text",
                    direction: "asc"
                },
                render: {
                    no_results: function(data, escape) {
                        return '<div class="no-results p-2 text-sm text-slate-500 italic">Không tìm thấy kết quả</div>';
                    }
                }
            };

            if (document.getElementById('select-advisor')) {
                new TomSelect("#select-advisor", commonConfig);
            }

            var tomMonitor = null;
            var tomSecretary = null;

            if (document.getElementById('select-monitor')) {
                tomMonitor = new TomSelect("#select-monitor", commonConfig);
            }
            if (document.getElementById('select-secretary')) {
                tomSecretary = new TomSelect("#select-secretary", commonConfig);
            }

            if (tomMonitor && tomSecretary) {
                tomMonitor.on('change', function(value) {
                    if (value && value === tomSecretary.getValue()) {
                        showToast('warning', 'Lớp trưởng và Thư ký không được là cùng một người!');
                        tomMonitor.clear();
                    }
                });

                tomSecretary.on('change', function(value) {
                    if (value && value === tomMonitor.getValue()) {
                        showToast('warning', 'Lớp trưởng và Thư ký không được là cùng một người!');
                        tomSecretary.clear();
                    }
                });
            }

            const formClass = document.getElementById('editClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');
            const fileInput = document.getElementById('student_file_input');
            const btnAddManual = document.getElementById('btn-add-manual');
            const newStudentsPreviewArea = document.getElementById('new-students-preview');
            const uploadErrorArea = document.getElementById('upload-error');
            const sendEmailInput = document.getElementById('send_email_import_input');

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

            const loadingModal = document.getElementById('loadingModal');
            const loadingTitle = document.getElementById('loading-modal-title');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            let newStudentsList = [];

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
                        `<p class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1"><span class="material-symbols-outlined !text-[14px]">warning</span> Có sinh viên bị trùng mã. Vui lòng xử lý.</p>`;
                }
                newStudentsPreviewArea.innerHTML = html;
            }

            window.removeNewStudent = function(index) {
                newStudentsList.splice(index, 1);
                renderNewStudentsTable();
            };

            if (btnAddManual) {
                btnAddManual.addEventListener('click', () => {
                    const mssvInput = document.getElementById('manual_mssv');
                    const nameInput = document.getElementById('manual_name');
                    const dobInput = document.getElementById('manual_dob');
                    const statusInput = document.getElementById('manual_status');
                    const mssv = mssvInput.value.trim().toUpperCase();
                    const name = nameInput.value.trim();

                    if (!mssv || !name) {
                        showToast("error", "Vui lòng nhập đầy đủ Mã SV và Họ Tên.");
                        return;
                    }
                    if (newStudentsList.some(s => s.mssv === mssv)) {
                        showToast("error", "Mã SV này đã tồn tại trong danh sách chuẩn bị thêm!");
                        return;
                    }
                    newStudentsList.push({
                        mssv: mssv,
                        name: name,
                        dob: dobInput.value,
                        status: statusInput.value,
                        is_duplicate: false
                    });
                    mssvInput.value = '';
                    nameInput.value = '';
                    dobInput.value = '';
                    renderNewStudentsTable();
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) return;
                    let formData = new FormData();
                    formData.append('file', file);
                    uploadErrorArea.classList.add('hidden');
                    newStudentsPreviewArea.innerHTML =
                        `<div class="mt-4 text-center text-slate-500 text-sm py-4">Đang đọc file...</div>`;

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
                    initStudentTableEvents();
                } catch (error) {
                    console.error("Lỗi tải lại bảng:", error);
                } finally {
                    searchSpinner.classList.add('hidden');
                    tableOverlay.classList.add('hidden');
                }
            }

            async function submitClassForm() {
                loadingModal.classList.remove('hidden');
                progressContainer.classList.add('hidden');
                loadingTitle.innerText = "Đang cập nhật...";
                btnPreSubmit.disabled = true;
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
                        showServerValidationErrors(data.errors, formClass);
                        return;
                    }
                    if (!data.success) throw new Error(data.message || 'Có lỗi xảy ra');

                    if (sendEmailInput.value == "1" && data.new_student_ids && data.new_student_ids.length >
                        0) {
                        await sendEmailsInBatches(data.new_student_ids, data.redirect_url);
                    } else {
                        window.location.href = data.redirect_url;
                    }
                } catch (error) {
                    loadingModal.classList.add('hidden');
                    btnPreSubmit.disabled = false;
                    showToast("error", error.message);
                }
            }

            async function sendEmailsInBatches(studentIds, redirectUrl) {
                progressContainer.classList.remove('hidden');
                loadingTitle.innerText = "Đang gửi Email...";
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
                if (redirectUrl) setTimeout(() => window.location.href = redirectUrl, 1000);
                else {
                    loadingModal.classList.add('hidden');
                    toggleActionBtns();
                }
            }

            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    clearValidationErrors();
                    uploadErrorArea.classList.add('hidden');
                    let hasError = false;
                    const requiredInputs = formClass.querySelectorAll('[required]');
                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            const fieldName = input.getAttribute('name');
                            showFieldError(formClass, fieldName, 'Vui lòng nhập thông tin này');
                            hasError = true;
                        }
                    });
                    if (newStudentsList.some(s => s.is_duplicate)) {
                        showToast("error", "Vui lòng xóa các sinh viên bị trùng mã!");
                        return;
                    }
                    if (hasError) return;

                    if (newStudentsList.length > 0) {
                        // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                        showConfirm(
                            'Lưu và Gửi Email',
                            'Có sinh viên mới được thêm. Cập nhật và tự động gửi email tài khoản cho họ?',
                            function() {
                                sendEmailInput.value = "1";
                                submitClassForm();
                            },
                            'primary'
                        );
                    } else {
                        // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                        showConfirm(
                            'Lưu thay đổi',
                            'Xác nhận cập nhật thông tin lớp học?',
                            submitClassForm,
                            'primary'
                        );
                    }
                });
            }

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
                        const span = btnDeleteSelected.querySelector('#btn-delete-text');
                        if (span) span.innerText = `Xóa (${selectedCount})`;
                    } else {
                        btnDeleteSelected.classList.add('hidden');
                    }
                }
            }

            function initStudentTableEvents() {
                const checkboxes = document.querySelectorAll('.student-checkbox');
                if (selectAll) {
                    selectAll.onclick = function() {
                        checkboxes.forEach(cb => cb.checked = selectAll.checked);
                        toggleActionBtns();
                    };
                }
                checkboxes.forEach(cb => cb.addEventListener('change', toggleActionBtns));

                // EVENT DELEGATION cho tbody để tránh mất sự kiện khi filter
                if (tableBody) {
                    tableBody.addEventListener('click', function(e) {
                        const target = e.target.closest('button');
                        if (!target) return;

                        // NÚT XÓA (ẨN)
                        if (target.classList.contains('btn-delete-student')) {
                            const code = target.getAttribute('data-code');
                            const url = target.closest('form') ? target.closest('form').action :
                                `/admin/students/${target.getAttribute('data-id')}`;

                            // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                            showConfirm(
                                'Xóa Sinh Viên?',
                                `Bạn có chắc muốn xóa sinh viên ${code} khỏi hệ thống?`,
                                async () => {
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
                                                target.closest('tr').remove();
                                                let count = parseInt(studentCountSpan.innerText.replace(
                                                    /[()]/g, '')) || 0;
                                                if (count > 0) studentCountSpan.innerText =
                                                    `(${count - 1})`;
                                                showToast("success", "Đã xóa thành công!");
                                            } else {
                                                showToast("error", "Lỗi: " + data.message);
                                            }
                                        } catch (e) {
                                            showToast("error", "Có lỗi xảy ra khi xóa.");
                                        }
                                    },
                                    'danger'
                            );
                        }

                        // NÚT SỬA
                        if (target.classList.contains('btn-edit-student')) {
                            const id = target.getAttribute('data-id');
                            const formEdit = document.getElementById('formEditStudent');
                            const editModal = document.getElementById('editStudentModal');
                            formEdit.action = `/admin/students/${id}`;
                            document.getElementById('edit_fullname').value = target.getAttribute(
                                'data-fullname');
                            document.getElementById('edit_email').value = target.getAttribute('data-email');
                            document.getElementById('edit_dob').value = target.getAttribute('data-dob');
                            document.getElementById('edit_status').value = target.getAttribute(
                                'data-status');
                            clearValidationErrors(formEdit);
                            editModal.classList.remove('hidden');
                        }

                        // NÚT GỬI MAIL ĐƠN LẺ
                        if (target.classList.contains('btn-send-single-email')) {
                            const id = target.getAttribute('data-id');
                            // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                            showConfirm(
                                'Gửi Email',
                                'Gửi thông tin tài khoản cho sinh viên này?',
                                () => sendEmailsInBatches([id], null),
                                'primary'
                            );
                        }
                    });
                }
            }
            initStudentTableEvents();

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

            const formEditStudentInner = document.getElementById('formEditStudent');
            const modalEditStudentInner = document.getElementById('editStudentModal');
            if (formEditStudentInner) {
                formEditStudentInner.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    clearValidationErrors(formEditStudentInner);
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
                            showServerValidationErrors(data.errors, formEditStudentInner);
                            return;
                        }
                        if (!data.success) throw new Error(data.message || "Lỗi cập nhật");
                        loadingModal.classList.add('hidden');
                        modalEditStudentInner.classList.add('hidden');
                        reloadStudentTable();
                        showToast("success", "Cập nhật sinh viên thành công!");
                    } catch (error) {
                        loadingModal.classList.add('hidden');
                        showToast("error", "Lỗi: " + error.message);
                    } finally {
                        btnSubmit.disabled = false;
                        btnSubmit.innerHTML = originalBtnText;
                    }
                });
            }

            if (btnDeleteSelected) {
                btnDeleteSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox')).filter(cb => cb
                        .checked).map(cb => cb.value);
                    if (ids.length === 0) return;
                    // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                    showConfirm(
                        'Xóa ' + ids.length + ' Sinh Viên?',
                        'Các sinh viên đã chọn sẽ bị chuyển vào thùng rác.',
                        async () => {
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
                                        showToast("success", "Đã xóa thành công!");
                                    } else {
                                        showToast("error", "Lỗi: " + data.message);
                                    }
                                } catch (e) {
                                    loadingModal.classList.add('hidden');
                                    showToast("error", "Có lỗi xảy ra khi xóa nhiều.");
                                }
                            },
                            'danger'
                    );
                });
            }

            if (btnExportExcel) {
                btnExportExcel.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('href');
                    // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                    showConfirm(
                        'Xuất Excel',
                        'Tải xuống danh sách sinh viên lớp này?',
                        () => window.location.href = url,
                        'primary'
                    );
                });
            }

            if (btnSendSelectedEmail) {
                btnSendSelectedEmail.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox')).filter(cb => cb
                        .checked).map(cb => cb.value);
                    if (ids.length === 0) return;
                    // CHUẨN HÓA LẠI THAM SỐ GỌI MODAL
                    showConfirm(
                        'Gửi Email Hàng Loạt',
                        `Gửi cho ${ids.length} sinh viên đã chọn?`,
                        () => sendEmailsInBatches(ids, null),
                        'primary'
                    );
                });
            }

            function chunkArray(myArray, chunk_size) {
                var results = [];
                while (myArray.length) {
                    results.push(myArray.splice(0, chunk_size));
                }
                return results;
            }
        });
    </script>
@endsection
