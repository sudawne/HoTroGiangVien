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

            {{-- FORM KHÔNG CẦN ACTION VÌ SẼ DÙNG JS FETCH --}}
            <form action="{{ route('admin.classes.store') }}" method="POST" enctype="multipart/form-data" class="p-6"
                id="createClassForm" novalidate>
                @csrf

                {{-- Input ẩn này chỉ dùng để JS tham chiếu logic, không submit trực tiếp --}}
                <input type="hidden" name="send_email" id="send_email_input" value="0">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="VD: 20DTHA1" required
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year') }}" required
                            placeholder="VD: 2020-2024"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name') }}" required
                            placeholder="VD: Đại học Công nghệ thông tin K20A"
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm">
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
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 transition-colors text-sm cursor-pointer">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}" {{ old('advisor_id') == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <div class="p-4 bg-blue-50 border border-blue-100 rounded-sm">
                            <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600 !text-[18px]">upload_file</span>
                                Import Danh sách Sinh viên (Tùy chọn)
                            </label>

                            <input type="file" name="student_file" id="student_file_input"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

                            <p class="text-xs text-slate-500 mt-2 ml-1">
                                Hỗ trợ file .xlsx, .csv. <span class="font-bold">Lưu ý:</span> Nếu mã SV bị trùng, quá trình
                                tạo lớp sẽ bị hủy.
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
                        <span class="material-symbols-outlined !text-[16px]">save</span> Lưu Lớp Học
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- INCLUDE MODAL ĐA NĂNG --}}
    @include('admin.classes.partials.universal_confirm_modal')
    @include('admin.classes.partials.loading_modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. KHAI BÁO BIẾN ---
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');
            const form = document.getElementById('createClassForm');
            const btnPreSubmit = document.getElementById('btn-pre-submit');

            // Modal Loading
            const loadingModal = document.getElementById('loadingModal');
            const loadingTitle = document.getElementById('loading-modal-title');
            const loadingDesc = document.getElementById('loading-modal-desc');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            // Modal Universal
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

            // --- 2. MODAL CONFIRM ---
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
                };
                const style = colors[btnColor] || colors.blue;

                uniBtnConfirm.className =
                    `px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 ${style.btn}`;
                uniIcon.className = `material-symbols-outlined text-[24px] ${style.icon}`;
                uniIconBg.className =
                    `flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center ${style.bg}`;

                // Đổi text nút Hủy
                const btnCancel = document.getElementById('btn-uni-cancel');
                btnCancel.innerText = onCancel ? 'Không gửi (Chỉ tạo)' : 'Hủy bỏ';

                // Reset trạng thái nút (nếu bị ẩn bởi showNotification trước đó)
                uniBtnConfirm.classList.remove('hidden');
                uniBtnCancel.classList.remove('hidden');

                universalModal.classList.remove('hidden');
            }

            // Hàm thông báo thành công đẹp
            function showNotification(message) {
                uniTitle.innerText = "Thành công!";
                uniDesc.innerText = message;
                uniIcon.innerText = "check_circle";
                uniIconBg.className =
                    "flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-green-100 text-green-600";

                uniBtnConfirm.classList.add('hidden');
                uniBtnCancel.classList.add('hidden');

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
                    if (cancelCallback) cancelCallback();
                    universalModal.classList.add('hidden');
                    pendingCallback = null;
                    cancelCallback = null;
                });
            }

            // --- 3. PREVIEW FILE ---
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
                                errorArea.innerText = 'Cảnh báo: File lỗi (dòng đỏ).';
                                errorArea.classList.remove('hidden');
                            }
                        }
                    }).catch(err => {
                        previewArea.innerHTML = '';
                        errorArea.innerText = 'Lỗi upload.';
                        errorArea.classList.remove('hidden');
                    });
                });
            }

            // --- 4. HÀM CHIA MẢNG ---
            function chunkArray(myArray, chunk_size) {
                var results = [];
                var arr = [...myArray];
                while (arr.length) {
                    results.push(arr.splice(0, chunk_size));
                }
                return results;
            }

            // --- 5. LOGIC SUBMIT FORM & GỬI MAIL AJAX ---
            async function processFormSubmission(shouldSendMail) {
                // Hiển thị loading tạo lớp
                loadingModal.classList.remove('hidden');
                progressContainer.classList.add('hidden'); // Chưa hiện progress
                loadingTitle.innerText = "Đang tạo dữ liệu...";
                loadingDesc.innerText = "Đang lưu thông tin lớp và import sinh viên...";
                btnPreSubmit.disabled = true;

                try {
                    const formData = new FormData(form);
                    // Ép buộc không gửi mail từ server (để client lo)
                    formData.set('send_email', '0');

                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest', // Bắt buộc để Controller trả về JSON
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (!data.success) throw new Error(data.message || 'Có lỗi xảy ra');

                    // Nếu tạo lớp thành công & người dùng chọn gửi mail
                    if (shouldSendMail && data.new_student_ids && data.new_student_ids.length > 0) {
                        await sendEmailsInBatches(data.new_student_ids, data.redirect_url);
                    } else {
                        // Không gửi mail -> Xong luôn
                        showNotification('Tạo lớp thành công!');
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1000);
                    }

                } catch (error) {
                    console.error(error);
                    loadingModal.classList.add('hidden');
                    btnPreSubmit.disabled = false;
                    alert('Lỗi: ' + error.message);
                }
            }

            // Hàm gửi mail batching
            async function sendEmailsInBatches(studentIds, redirectUrl) {
                // Chuyển UI sang trạng thái gửi mail
                progressContainer.classList.remove('hidden');
                loadingTitle.innerText = "Đang gửi Email...";
                loadingDesc.innerText = "Vui lòng không tắt trình duyệt.";

                const total = studentIds.length;
                let processed = 0;
                const batches = chunkArray(studentIds, 3); // Gửi 3 mail/lần

                progressBar.style.width = "0%";
                progressText.innerText = `Đã gửi 0/${total}`;

                for (const batch of batches) {
                    try {
                        const res = await fetch('{{ route('admin.classes.send_emails') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                student_ids: batch
                            })
                        });

                        // Cập nhật tiến độ dù lỗi hay không
                        processed += batch.length;
                        const percent = Math.round((processed / total) * 100);
                        progressBar.style.width = `${percent}%`;
                        progressText.innerText = `Đã gửi ${processed}/${total} (${percent}%)`;

                    } catch (err) {
                        console.error('Lỗi gửi mail:', err);
                    }
                }

                // Xong hết -> Thông báo và chuyển trang
                setTimeout(() => {
                    loadingModal.classList.add('hidden');
                    showNotification(`Đã hoàn tất! Gửi ${processed}/${total} email.`);
                    setTimeout(() => {
                        window.location.href = redirectUrl;
                    }, 1500);
                }, 500);
            }

            // --- 6. SỰ KIỆN NÚT LƯU ---
            if (btnPreSubmit) {
                btnPreSubmit.addEventListener('click', function() {
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return;
                    }

                    if (fileInput.files.length > 0) {
                        showConfirm({
                            title: 'Gửi thông tin tài khoản?',
                            message: 'Bạn có muốn gửi email tài khoản cho danh sách sinh viên vừa import không?',
                            btnText: 'Đồng ý gửi',
                            btnColor: 'blue',
                            icon: 'mark_email_unread',
                            callback: function() {
                                processFormSubmission(true); // Có gửi mail
                            },
                            onCancel: function() {
                                processFormSubmission(false); // Không gửi mail
                            }
                        });
                    } else {
                        // Không có file -> Lưu bình thường
                        processFormSubmission(false);
                    }
                });
            }
        });
    </script>
@endsection
