@extends('layouts.admin')
@section('title', 'Import Xóa Học Phần')

@section('content')
    {{-- Container mở rộng full màn hình --}}
    <div class="w-full h-[calc(100vh-80px)] flex flex-col">

        @if (!isset($previewData))
            {{-- FORM GIAI ĐOẠN 1: UPLOAD & PREVIEW --}}
            <form action="{{ route($routePrefix . 'course_cancellations.preview') }}" method="POST"
                enctype="multipart/form-data" class="flex flex-col h-full">
                @csrf
            @else
                {{-- FORM GIAI ĐOẠN 2: CONFIRM STORE --}}
                <form action="{{ route($routePrefix . 'course_cancellations.store_import') }}" method="POST"
                    class="flex flex-col h-full">
                    @csrf
                    <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
                    <input type="hidden" name="semester_id" value="{{ $semester_id }}">
        @endif

        <div class="rounded-lg mb-4 flex flex-col xl:flex-row xl:items-center justify-between gap-4 sticky top-0 z-20">

            <x-page-header title="Nhập dữ liệu" description="Tải lên file Excel để cập nhật danh sách hủy học phần"
                :routePrefix="$routePrefix" :breadcrumbs="[
                    ['label' => 'Hủy học phần', 'url' => route($routePrefix . 'course_cancellations.index')],
                    ['label' => 'Nhập dữ liệu'],
                ]" />

            <div class="flex flex-wrap items-center gap-4">

                <div class="flex items-center gap-2 border-r border-slate-300 dark:border-slate-700 pr-4">
                    <span class="material-symbols-outlined text-slate-400 !text-[20px]">calendar_month</span>
                    <select name="semester_id"
                        class="bg-slate-50 dark:bg-slate-800 border-none text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-2 pr-8 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        @foreach ($semesters as $sem)
                            <option value="{{ $sem->id }}"
                                {{ isset($semester_id) && $semester_id == $sem->id ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3">
                    @if (!isset($previewData))
                        <label for="file-upload"
                            class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium text-sm transition-colors border border-slate-200 dark:border-slate-600">
                            <span class="material-symbols-outlined !text-[20px] text-green-600">table_view</span>
                            <span id="toolbar-filename">Chọn file Excel</span>
                            <input id="file-upload" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv"
                                required onchange="updateFileName(this)" />
                        </label>

                        <button type="submit"
                            class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm shadow-indigo-500/30">
                            <span class="material-symbols-outlined !text-[20px]">visibility</span>
                            Xem dữ liệu
                        </button>
                    @else
                        <div
                            class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium border border-blue-100 dark:border-blue-800 flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[18px]">description</span>
                            File: {{ $selected_file_name ?? 'Data.xlsx' }}
                        </div>

                        <a href="{{ route($routePrefix . 'course_cancellations.import') }}"
                            class="px-4 py-2 text-slate-500 hover:text-slate-700 font-medium text-sm">
                            Chọn lại
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- === PHẦN 2: KHU VỰC NỘI DUNG (DƯỚI) === --}}
        <div
            class="flex-1 bg-white dark:bg-[#1e1e2d] rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative flex flex-col">

            @if (!isset($previewData))
                {{-- VIEW 1: DROPZONE UPLOAD --}}
                <div class="flex-1 flex flex-col items-center justify-center p-10 border-4 border-dashed border-slate-100 dark:border-slate-800 m-4 rounded-xl bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group cursor-pointer"
                    onclick="document.getElementById('file-upload').click()">
                    <div
                        class="bg-white dark:bg-slate-700 p-6 rounded-full shadow-sm mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span
                            class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-500 group-hover:text-primary transition-colors">cloud_upload</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200 mb-2">Tải lên danh sách Xóa Học Phần
                    </h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Kéo thả file vào đây hoặc bấm nút "Chọn file Excel" ở
                        trên</p>
                    <div class="flex gap-4 text-xs text-slate-400 font-mono">
                        <span
                            class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.XLSX</span>
                        <span
                            class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.CSV</span>
                    </div>
                </div>
            @else
                {{-- VIEW 2: BẢNG DỮ LIỆU PREVIEW --}}
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">
                                    MSSV</th>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">
                                    Họ tên (Excel)</th>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">
                                    Mã Môn</th>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">
                                    Tên Môn (Excel)</th>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">
                                    Lý do</th>
                                <th
                                    class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-right">
                                    Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach ($previewData as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors {{ !$row['student_exists'] || !$row['subject_exists'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}"
                                    id="row-{{ $row['student_code'] }}">

                                    {{-- Cột MSSV --}}
                                    <td
                                        class="p-4 font-medium font-mono {{ !$row['student_exists'] ? 'text-red-600' : 'text-primary' }}">
                                        {{ $row['student_code'] }}
                                    </td>

                                    {{-- Cột Họ tên --}}
                                    <td class="p-4 font-medium dark:text-slate-300">
                                        {{ $row['student_name'] }}
                                    </td>

                                    {{-- Cột Mã Môn --}}
                                    <td
                                        class="p-4 font-mono {{ !$row['subject_exists'] ? 'text-red-600 font-bold' : 'text-slate-500' }}">
                                        {{ $row['subject_code'] }}
                                    </td>

                                    {{-- Cột Tên Môn --}}
                                    <td class="p-4 text-slate-500 dark:text-slate-400">
                                        {{ $row['subject_name'] }}
                                    </td>

                                    {{-- Cột Lý do --}}
                                    <td class="p-4 text-center">
                                        <span class="badge-gray">{{ $row['reason'] }}</span>
                                    </td>

                                    {{-- Cột Trạng thái & Action --}}
                                    <td class="p-4 text-right">
                                        @if ($row['student_exists'] && $row['subject_exists'])
                                            <div
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/50">
                                                <span
                                                    class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400">check_circle</span>
                                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Hợp
                                                    lệ</span>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-end gap-2">
                                                @if (!$row['subject_exists'])
                                                    <span class="badge-orange">Sai Mã Môn</span>
                                                @endif

                                                @if (!$row['student_exists'])
                                                    {{-- Nút Thêm Nhanh Sinh Viên --}}
                                                    <button type="button"
                                                        onclick="openQuickAddModal('{{ $row['student_code'] }}', '{{ $row['student_name'] }}', '', '{{ $row['class_code'] ?? '' }}')"
                                                        class="group inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm hover:border-primary hover:ring-1 hover:ring-primary/20 hover:text-primary transition-all duration-200">
                                                        <span
                                                            class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-primary transition-colors">person_add</span>
                                                        <span
                                                            class="text-xs font-semibold text-slate-600 dark:text-slate-300 group-hover:text-primary">Thêm
                                                            SV</span>
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- NÚT XÁC NHẬN Ở DƯỚI CÙNG (Sticky Bottom) --}}
                <div
                    class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <div class="text-sm text-slate-500">
                        Đang xem trước <span
                            class="font-bold text-slate-900 dark:text-white">{{ count($previewData) }}</span> dòng dữ liệu.
                        <span class="ml-2 text-xs italic text-orange-500">(Chỉ lưu các dòng hợp lệ)</span>
                    </div>
                    <button type="submit"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-green-600/20 transform hover:-translate-y-0.5 transition-all">
                        <span class="material-symbols-outlined">save</span>
                        Xác nhận & Lưu vào hệ thống
                    </button>
                </div>
            @endif

        </div>

        </form> {{-- Đóng thẻ Form --}}
    </div>

    {{-- === MODAL THÊM SINH VIÊN NHANH === --}}
    <div id="quickAddModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">

                    {{-- Header Modal --}}
                    <div
                        class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-outlined text-blue-600">person_add</span>
                            </div>
                            <div class="mt-3 text-center sm:ml-0 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white" id="modal-title">
                                    Thêm sinh viên vào CSDL
                                </h3>
                                <div class="mt-1">
                                    <p class="text-sm text-slate-500">
                                        Sinh viên này chưa có trong hệ thống. Vui lòng kiểm tra và bổ sung thông tin.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form id="quickAddForm">
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Mã
                                        số SV <span class="text-red-500">*</span></label>
                                    <input type="text" id="qa_mssv" name="mssv" readonly
                                        class="w-full bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary cursor-not-allowed text-slate-500">
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ngày
                                        sinh</label>
                                    <input type="date" id="qa_dob" name="dob"
                                        class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Họ
                                    và tên <span class="text-red-500">*</span></label>
                                <input type="text" id="qa_fullname" name="fullname"
                                    class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary"
                                    required>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email
                                    <span class="text-red-500">*</span></label>
                                <input type="email" id="qa_email" name="email"
                                    class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary"
                                    required>
                                <p class="text-[10px] text-slate-400 mt-1">Mặc định: MSSV@vnkgu.edu.vn</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lớp
                                    sinh hoạt</label>
                                <select id="qa_class_id" name="class_id"
                                    class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Chọn lớp (Nếu có) --</option>
                                    {{-- Lưu ý: Biến $classes cần được truyền từ Controller xuống view này --}}
                                    @if (isset($classes))
                                        @foreach ($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->code }} -
                                                {{ $c->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div
                            class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit" id="btn-qa-save"
                                class="inline-flex w-full justify-center rounded-sm bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all">
                                <span class="material-symbols-outlined !text-[18px] mr-1">save</span> Lưu vào CSDL
                            </button>
                            <button type="button" onclick="closeQuickAddModal()"
                                class="mt-3 inline-flex w-full justify-center rounded-sm bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 sm:mt-0 sm:w-auto">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        // 1. Script Upload File
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('toolbar-filename');
            if (input.files && input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
                fileNameSpan.classList.add('text-primary', 'font-bold');
            } else {
                fileNameSpan.textContent = 'Chọn file Excel';
                fileNameSpan.classList.remove('text-primary', 'font-bold');
            }
        }

        // 2. Script Modal & Ajax
        const modal = document.getElementById('quickAddModal');
        const form = document.getElementById('quickAddForm');
        const btnSave = document.getElementById('btn-qa-save');

        function removeVietnameseTones(str) {
            str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
            str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
            str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
            str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
            str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
            str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
            str = str.replace(/đ/g, "d");
            str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
            str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
            str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
            str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
            str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
            str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
            str = str.replace(/Đ/g, "D");
            return str.toLowerCase();
        }

        function openQuickAddModal(mssv, fullname, dobRaw, classCodeRaw) {
            // 1. Điền thông tin cơ bản
            document.getElementById('qa_mssv').value = mssv;
            document.getElementById('qa_fullname').value = fullname;

            if (fullname) {
                const parts = fullname.trim().split(/\s+/);
                const lastName = parts[parts.length - 1];
                const slugName = removeVietnameseTones(lastName);
                document.getElementById('qa_email').value = `${slugName}${mssv}@vnkgu.edu.vn`;
            }

            const dobInput = document.getElementById('qa_dob');
            if (dobRaw) {
                dobInput.value = dobRaw;
            } else {
                dobInput.value = '';
            }

            // 2. Auto-select class
            const classSelect = document.getElementById('qa_class_id');
            classSelect.selectedIndex = 0;

            if (classCodeRaw && classCodeRaw.trim() !== '') {
                const targetCode = classCodeRaw.trim().toUpperCase();
                for (let i = 0; i < classSelect.options.length; i++) {
                    const optionText = classSelect.options[i].text.toUpperCase();
                    if (optionText.includes(targetCode)) {
                        classSelect.selectedIndex = i;
                        break;
                    }
                }
            }

            modal.classList.remove('hidden');
        }

        function closeQuickAddModal() {
            modal.classList.add('hidden');
            form.reset();
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const originalText = btnSave.innerHTML;
            btnSave.innerHTML =
                '<span class="material-symbols-outlined animate-spin !text-[18px] mr-1">sync</span> Đang lưu...';
            btnSave.disabled = true;

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            // Lưu ý: Dùng lại route quick add của warning nếu logic giống hệt (thêm sinh viên vào bảng students)
            // Hoặc tạo route mới cho course cancellation nếu cần.
            // Ở đây tôi dùng lại route cũ theo yêu cầu "giống bên warning"
            fetch('{{ route('admin.academic_warnings.quick_add_student') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        closeQuickAddModal();

                        // Cập nhật dòng UI thành Hợp lệ
                        const row = document.getElementById('row-' + data.mssv);
                        if (row) {
                            row.classList.remove('bg-red-50/50', 'dark:bg-red-900/10');
                            row.classList.add('bg-green-50/30');

                            // Tìm cột chứa nút và thay đổi nội dung
                            const actionCell = row.lastElementChild;
                            actionCell.innerHTML = `
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/50 animate-pulse">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400">check_circle</span>
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Đã thêm</span>
                            </div>`;

                            // Đổi màu text cột MSSV
                            row.firstElementChild.classList.remove('text-red-600');
                            row.firstElementChild.classList.add('text-primary');
                        }
                    } else {
                        alert('Lỗi: ' + result.message);
                    }
                })
                .catch(error => {
                    console.error(error);
                    alert('Lỗi kết nối server');
                })
                .finally(() => {
                    btnSave.innerHTML = originalText;
                    btnSave.disabled = false;
                });
        });
    </script>

    {{-- STYLE --}}
    <style>
        .badge-red {
            @apply px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 border border-red-200;
        }

        .badge-orange {
            @apply px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200;
        }

        .badge-gray {
            @apply px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200;
        }
    </style>
@endsection
