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
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="code"></p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year') }}" required
                            placeholder="VD: 2020-2024"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="academic_year"></p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="VD: Đại học Công nghệ thông tin K20A"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="name"></p>
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
                        <select name="advisor_id" id="select-advisor" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}" {{ old('advisor_id') == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-red-500 text-xs mt-1 error-msg" data-field="advisor_id"></p>
                    </div>

                    {{-- PHẦN CHỌN BAN CÁN SỰ --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Lớp trưởng
                        </label>
                        <select name="monitor_id" id="select-monitor" disabled
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                            <option value="">-- Chưa có danh sách sinh viên --</option>
                        </select>
                        <p class="text-[10px] text-slate-400 mt-1 italic">Bạn cần tạo lớp và thêm sinh viên trước khi chọn
                            BCS.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Thư ký / Lớp phó
                        </label>
                        <select name="secretary_id" id="select-secretary" disabled
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                            <option value="">-- Chưa có danh sách sinh viên --</option>
                        </select>
                    </div>

                    {{-- KHU VỰC IMPORT VÀ THÊM TAY --}}
                    <div class="md:col-span-2 mt-2">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 !text-[18px]">group_add</span>
                                Danh sách Sinh viên (Import hoặc Thêm thủ công)
                            </label>

                            <div class="flex items-center gap-4 mb-3">
                                <input type="file" name="student_file_temp" id="student_file_input" accept=".xlsx, .csv"
                                    class="block w-full md:w-1/2 text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />
                            </div>

                            <div class="bg-white p-3 border border-slate-200 rounded-sm mb-4">
                                <h4 class="text-xs font-bold text-slate-500 uppercase mb-2">Thêm nhanh sinh viên</h4>
                                <div class="flex flex-wrap gap-3 items-end">
                                    <div class="flex-1 min-w-[120px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Mã SV *</label>
                                        <input type="text" id="manual_mssv"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm uppercase">
                                    </div>
                                    <div class="flex-1 min-w-[150px]">
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Họ và Tên *</label>
                                        <input type="text" id="manual_name" placeholder="VD: Nguyễn Văn A"
                                            class="w-full px-2 py-1.5 border border-slate-300 rounded text-sm">
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

                            <p class="text-red-500 text-xs mt-1 error-msg" data-field="student_file"></p>
                            <p id="upload-error" class="text-red-500 text-xs mt-2 hidden font-bold"></p>

                            <div id="preview-area" class="overflow-x-auto mt-2">
                                <p class="text-sm text-slate-500 italic py-4 text-center">Chưa có sinh viên nào trong danh
                                    sách.</p>
                            </div>
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

    {{-- MODAL CUSTOM ĐỂ HỎI GỬI EMAIL HAY KHÔNG --}}
    <div id="askEmailModal"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm"
        aria-modal="true">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md p-6 relative transform transition-all scale-100">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center shrink-0">
                    <span class="material-symbols-outlined text-blue-600 !text-[24px]">forward_to_inbox</span>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-slate-800">Gửi thông tin tài khoản?</h3>
                    <p class="text-sm text-slate-500">Bạn muốn xử lý thế nào với danh sách sinh viên này?</p>
                </div>
            </div>
            <div class="text-sm text-slate-600 mb-6 bg-slate-50 p-3 rounded border border-slate-200">
                Nếu chọn <b>"Lưu & Gửi Email"</b>, hệ thống sẽ tạo tài khoản và tự động gửi email thông báo mật khẩu cho
                từng sinh viên. Quá trình này có thể mất chút thời gian.
            </div>
            <div class="flex flex-col sm:flex-row items-center justify-end gap-3">
                <button type="button" id="btn-cancel-modal"
                    class="w-full sm:w-auto px-4 py-2 border border-slate-300 rounded text-slate-700 font-medium hover:bg-slate-50 transition-colors">
                    Hủy bỏ thao tác
                </button>
                <div class="flex w-full sm:w-auto gap-2">
                    <button type="button" id="btn-only-save"
                        class="flex-1 px-4 py-2 bg-slate-800 text-white rounded font-medium hover:bg-slate-700 transition-colors shadow-sm whitespace-nowrap">
                        Chỉ Lưu
                    </button>
                    <button type="button" id="btn-save-and-mail"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded font-medium hover:bg-blue-700 transition-colors shadow-sm whitespace-nowrap flex items-center justify-center gap-1.5">
                        <span class="material-symbols-outlined !text-[16px]">send</span> Gửi Mail
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('admin.classes.partials.loading_modal')

    {{-- Script Tom Select --}}
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. SETUP TOM SELECT ---
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

            try {
                new TomSelect("#select-monitor", commonConfig);
                new TomSelect("#select-secretary", commonConfig);
            } catch (e) {
                console.log('TomSelect Monitor/Secretary init skipped');
            }


            // --- 2. LOGIC CŨ GIỮ NGUYÊN ---
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const form = document.getElementById('createClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');
            const btnAddManual = document.getElementById('btn-add-manual');

            const loadingModal = document.getElementById('loadingModal');
            const loadingTitle = document.getElementById('loading-modal-title');
            const loadingDesc = document.getElementById('loading-modal-desc');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            // Các thành phần của Modal Hỏi Email
            const askEmailModal = document.getElementById('askEmailModal');
            const btnCancelModal = document.getElementById('btn-cancel-modal');
            const btnOnlySave = document.getElementById('btn-only-save');
            const btnSaveAndMail = document.getElementById('btn-save-and-mail');

            let studentsList = [];

            function clearValidationErrors() {
                document.querySelectorAll('.error-msg').forEach(el => el.innerText = '');
                document.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
            }

            function showFieldError(fieldName, message) {
                const inputElement = form.querySelector(`[name="${fieldName}"]`);
                const errorElement = document.querySelector(`.error-msg[data-field="${fieldName}"]`);
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

            function showServerValidationErrors(errors) {
                for (const [field, messages] of Object.entries(errors)) {
                    showFieldError(field, messages[0]);
                }
            }

            function renderStudentsTable() {
                if (studentsList.length === 0) {
                    previewArea.innerHTML =
                        '<p class="text-sm text-slate-500 italic text-center py-4">Chưa có sinh viên nào trong danh sách.</p>';
                    return;
                }
                let html = `
                    <table class="w-full text-left text-sm border-collapse bg-white shadow-sm rounded-sm overflow-hidden border border-slate-200">
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
                studentsList.forEach((s, index) => {
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
                                <button type="button" class="text-red-500 hover:text-red-700" onclick="removeStudent(${index})" title="Xóa dòng này">
                                    <span class="material-symbols-outlined !text-[18px]">delete</span>
                                </button>
                            </td>
                        </tr>
                    `;
                });
                html += `</tbody></table>`;
                if (studentsList.some(s => s.is_duplicate)) {
                    html +=
                        `<p class="text-red-500 text-xs mt-2 font-bold flex items-center gap-1"><span class="material-symbols-outlined !text-[14px]">warning</span> Có sinh viên bị trùng mã. Vui lòng xử lý.</p>`;
                }
                previewArea.innerHTML = html;
            }

            window.removeStudent = function(index) {
                studentsList.splice(index, 1);
                renderStudentsTable();
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
                    if (studentsList.some(s => s.mssv === mssv)) {
                        showToast("error", "Mã SV này đã tồn tại trong danh sách!");
                        return;
                    }
                    studentsList.push({
                        mssv: mssv,
                        name: name,
                        dob: dobInput.value,
                        status: statusInput.value,
                        is_duplicate: false
                    });
                    mssvInput.value = '';
                    nameInput.value = '';
                    dobInput.value = '';
                    renderStudentsTable();
                });
            }

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) {
                        studentsList = [];
                        renderStudentsTable();
                        return;
                    }
                    let formData = new FormData();
                    formData.append('file', file);
                    previewArea.innerHTML =
                        `<div class="mt-4 text-center text-slate-500 text-sm py-4">Đang đọc dữ liệu...</div>`;
                    errorArea.classList.add('hidden');

                    fetch('{{ route('admin.classes.upload.preview') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json()).then(data => {
                        if (data.error) {
                            studentsList = [];
                            renderStudentsTable();
                            errorArea.innerText = data.error;
                            errorArea.classList.remove('hidden');
                        } else {
                            if (data.data) {
                                studentsList = data.data;
                                renderStudentsTable();
                            }
                        }
                    }).catch(err => {
                        studentsList = [];
                        renderStudentsTable();
                        errorArea.innerText = 'Lỗi kết nối hoặc định dạng file.';
                        errorArea.classList.remove('hidden');
                    });
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

            async function processFormSubmission(shouldSendMail) {
                askEmailModal.classList.replace('flex', 'hidden'); // Ẩn modal hỏi
                loadingModal.classList.remove('hidden');

                if (shouldSendMail) {
                    progressContainer.classList.remove('hidden');
                    loadingTitle.innerText = "Đang xử lý dữ liệu và gửi Email...";
                    loadingDesc.innerText = "Vui lòng chờ. Quá trình này có thể mất vài phút...";
                } else {
                    progressContainer.classList.add('hidden');
                    loadingTitle.innerText = "Đang lưu dữ liệu...";
                    loadingDesc.innerText = "Đang tạo lớp học và danh sách sinh viên...";
                }

                btnPreSubmit.disabled = true;
                clearValidationErrors();

                try {
                    const formData = new FormData(form);
                    formData.set('send_email', shouldSendMail ? '1' : '0');
                    formData.delete('student_file_temp');

                    if (studentsList.length > 0) {
                        formData.set('students_list', JSON.stringify(studentsList));
                    }

                    const response = await fetch(form.action, {
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
                        showServerValidationErrors(data.errors);
                        showToast("error", "Vui lòng kiểm tra lại thông tin nhập vào.");
                        return;
                    }

                    if (!data.success) throw new Error(data.message || 'Có lỗi xảy ra từ máy chủ');

                    // TH 1: Có chọn gửi mail và có SV mới
                    if (shouldSendMail && data.new_student_ids && data.new_student_ids.length > 0) {
                        await sendEmailsInBatches(data.new_student_ids, data.redirect_url);
                    }
                    // TH 2: Không gửi mail hoặc không có SV nào được thêm
                    else {
                        showToast("success", "Đã lưu lớp học thành công!");
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 800);
                    }
                } catch (error) {
                    loadingModal.classList.add('hidden');
                    btnPreSubmit.disabled = false;
                    showToast("error", error.message);
                }
            }

            async function sendEmailsInBatches(studentIds, redirectUrl) {
                progressBar.style.width = "0%";
                const total = studentIds.length;
                let processed = 0;
                const batches = chunkArray(studentIds, 3);
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
                    } catch (err) {
                        console.error("Lỗi khi gửi email batch: ", err);
                    }
                }

                showToast("success", "Tạo lớp và gửi email hoàn tất!");
                setTimeout(() => {
                    window.location.href = redirectUrl;
                }, 1000);
            }

            // XỬ LÝ SỰ KIỆN KHI BẤM NÚT LƯU
            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    clearValidationErrors();
                    errorArea.classList.add('hidden');
                    let hasError = false;
                    const requiredInputs = form.querySelectorAll('[required]');
                    const customMessages = {
                        'code': 'Vui lòng nhập Mã lớp.',
                        'academic_year': 'Vui lòng nhập Niên khóa.',
                        'name': 'Vui lòng nhập Tên lớp.',
                        'advisor_id': 'Vui lòng chọn Cố vấn học tập.'
                    };

                    requiredInputs.forEach(input => {
                        if (!input.value.trim()) {
                            const fieldName = input.getAttribute('name');
                            const message = customMessages[fieldName] ||
                                'Vui lòng nhập thông tin này';
                            showFieldError(fieldName, message);
                            hasError = true;
                        }
                    });

                    if (studentsList.some(s => s.is_duplicate)) {
                        showToast("error", "Vui lòng xóa các sinh viên bị trùng mã trước khi lưu!");
                        return;
                    }
                    if (hasError) {
                        showToast("warning", "Vui lòng điền đầy đủ các thông tin bắt buộc.");
                        return;
                    }

                    // HIỂN THỊ MODAL NẾU CÓ SINH VIÊN
                    if (studentsList.length > 0) {
                        askEmailModal.classList.replace('hidden', 'flex');
                    } else {
                        // Không có sinh viên, chỉ tạo lớp
                        processFormSubmission(false);
                    }
                });
            }

            // SỰ KIỆN CỦA CÁC NÚT TRONG MODAL
            btnCancelModal.addEventListener('click', () => {
                askEmailModal.classList.replace('flex', 'hidden');
            });

            btnOnlySave.addEventListener('click', () => {
                document.getElementById('send_email_input').value = "0";
                processFormSubmission(false);
            });

            btnSaveAndMail.addEventListener('click', () => {
                document.getElementById('send_email_input').value = "1";
                processFormSubmission(true);
            });

        });
    </script>
@endsection
