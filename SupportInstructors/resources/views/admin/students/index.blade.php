@extends('layouts.admin')
@section('title', 'Danh sách Sinh viên')

@section('content')
    <div class="w-full px-4 py-6" x-data="{ showImportModal: false, showCreateModal: false }">

        {{-- Header & Toolbar --}}
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Hồ sơ Sinh viên</h1>
                <p class="text-xs text-slate-500">Quản lý thông tin và trạng thái học tập</p>
            </div>

            <div class="flex gap-2">
                {{-- CÁC NÚT HÀNG LOẠT (Mặc định ẩn) --}}
                <button type="button" id="btn-restore-selected"
                    class="hidden flex items-center gap-2 px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded-sm hover:bg-blue-700 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">history</span> Khôi phục
                </button>

                <button type="button" id="btn-delete-selected"
                    class="hidden flex items-center gap-2 px-3 py-2 bg-red-600 text-white text-sm font-semibold rounded-sm hover:bg-red-700 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">visibility_off</span> Ẩn đã chọn
                </button>

                {{-- CÁC NÚT CHỨC NĂNG --}}
                <button @click="showImportModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">upload_file</span> Import Excel
                </button>
                <button @click="showCreateModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-sm hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">add</span> Thêm mới
                </button>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-sm shadow-sm mb-6">
            <form action="{{ route('admin.students.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm kiếm theo Tên hoặc MSSV..."
                        class="w-full px-4 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                </div>
                <div class="w-full md:w-64">
                    <select name="class_id"
                        class="w-full px-4 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                        <option value="">-- Tất cả Lớp --</option>
                        @foreach ($classes as $cls)
                            <option value="{{ $cls->id }}" {{ request('class_id') == $cls->id ? 'selected' : '' }}>
                                {{ $cls->code }} - {{ $cls->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit"
                    class="px-6 py-2 bg-slate-800 text-white text-sm font-semibold rounded-sm hover:bg-slate-700 transition-colors">
                    Lọc dữ liệu
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm overflow-hidden relative">

            {{-- Loading Overlay --}}
            <div id="table-loading"
                class="absolute inset-0 bg-white/60 dark:bg-slate-900/60 z-20 hidden flex items-center justify-center backdrop-blur-[1px]">
                <div class="flex flex-col items-center">
                    <span class="material-symbols-outlined animate-spin text-primary text-4xl mb-2">progress_activity</span>
                    <span class="text-sm font-medium text-slate-600 dark:text-slate-300">Đang xử lý...</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3 w-10 text-center">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4 cursor-pointer">
                            </th>
                            <th class="px-6 py-3">MSSV</th>
                            <th class="px-6 py-3">Họ và Tên</th>
                            <th class="px-6 py-3">Lớp</th>
                            <th class="px-6 py-3">Ngày sinh</th>
                            <th class="px-6 py-3 w-40">Trạng thái</th>
                            <th class="px-6 py-3 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody id="students-table-body" class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $st)
                            @php $isTrashed = $st->trashed(); @endphp
                            <tr
                                class="transition-colors group {{ $isTrashed ? 'bg-slate-100/70 dark:bg-slate-900/50 opacity-75' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                                <td class="px-6 py-3 text-center">
                                    <input type="checkbox" value="{{ $st->id }}"
                                        data-trashed="{{ $isTrashed ? 'true' : 'false' }}"
                                        class="student-checkbox select-item rounded border-gray-300 text-primary cursor-pointer">
                                </td>
                                <td
                                    class="px-6 py-3 font-mono font-medium {{ $isTrashed ? 'text-slate-400 decoration-slate-400' : 'text-primary' }}">
                                    {{ $st->student_code }}
                                </td>
                                <td class="px-6 py-3 font-medium text-slate-800 dark:text-white">
                                    <div class="flex items-center gap-2">
                                        <span class="{{ $isTrashed ? 'text-slate-500 line-through' : '' }}">
                                            {{ $st->fullname }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-3 {{ $isTrashed ? 'text-slate-400' : '' }}">
                                    {{ $st->class->code ?? '---' }}
                                </td>
                                <td class="px-6 py-3 text-slate-500 {{ $isTrashed ? 'text-slate-400' : '' }}">
                                    {{ $st->dob ? \Carbon\Carbon::parse($st->dob)->format('d/m/Y') : '--' }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap">
                                    @if ($isTrashed)
                                        <span class="text-xs text-slate-400 italic font-medium">Vô hiệu hóa</span>
                                    @else
                                        @php
                                            $statusClass = match ($st->status) {
                                                'studying' => 'bg-emerald-100 text-emerald-700',
                                                'dropped' => 'bg-red-100 text-red-700',
                                                'reserved' => 'bg-yellow-100 text-yellow-700',
                                                'graduated' => 'bg-blue-100 text-blue-700',
                                                default => 'bg-slate-100 text-slate-700',
                                            };
                                            $statusText = match ($st->status) {
                                                'studying' => 'Đang học',
                                                'dropped' => 'Thôi học',
                                                'reserved' => 'Bảo lưu',
                                                'graduated' => 'Tốt nghiệp',
                                                default => Str::upper($st->status),
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex px-2 py-1 rounded text-[10px] font-bold {{ $statusClass }}">
                                            {{ $statusText }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($isTrashed)
                                            {{-- NÚT KHÔI PHỤC --}}
                                            <button type="button"
                                                onclick="restoreStudent({{ $st->id }}, '{{ $st->student_code }}')"
                                                class="text-blue-500 hover:text-blue-700 p-1.5 hover:bg-blue-50 rounded transition-colors"
                                                title="Khôi phục">
                                                <span class="material-symbols-outlined !text-[20px]">history</span>
                                            </button>
                                        @else
                                            <a href="{{ route('admin.students.show', $st->id) }}"
                                                class="text-blue-600 hover:text-blue-800 p-1.5 hover:bg-blue-50 rounded transition-colors"
                                                title="Xem hồ sơ chi tiết">
                                                <span class="material-symbols-outlined !text-[20px]">id_card</span>
                                            </a>
                                            <button type="button"
                                                onclick="deleteStudent({{ $st->id }}, '{{ $st->student_code }}')"
                                                class="text-red-500 hover:text-red-700 p-1.5 hover:bg-red-50 rounded transition-colors"
                                                title="Ẩn sinh viên">
                                                <span class="material-symbols-outlined !text-[20px]">visibility_off</span>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500 italic">Không tìm thấy sinh
                                    viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $students->links() }}
            </div>
        </div>

        {{-- MODAL IMPORT (Giữ nguyên) --}}
        <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-md rounded-lg shadow-xl overflow-hidden"
                @click.away="showImportModal = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800">Import Sinh viên từ Excel</h3>
                    <button @click="showImportModal = false" class="text-slate-400 hover:text-red-500"><span
                            class="material-symbols-outlined">close</span></button>
                </div>
                <form action="{{ route('admin.imports.storeStudent') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-semibold mb-2">Chọn Lớp cần thêm SV</label>
                        <select name="class_id" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary">
                            @foreach ($classes as $cls)
                                <option value="{{ $cls->id }}">{{ $cls->code }} - {{ $cls->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold mb-2">File Danh sách (.xlsx, .csv)</label>
                        <input type="file" name="file" required
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20" />
                        <p class="text-xs text-slate-400 mt-2">File bắt đầu đọc từ dòng số 8 (như mẫu)</p>
                    </div>
                    <div class="pt-4 flex justify-end gap-3">
                        <button type="button" @click="showImportModal = false"
                            class="px-4 py-2 border rounded-sm text-slate-600 hover:bg-slate-50">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-sm hover:bg-primary/90">Tiến
                            hành Import</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL THÊM SINH VIÊN (Giữ nguyên) --}}
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

                <form id="formCreateStudent" action="{{ route('admin.students.store') }}" method="POST"
                    class="p-6">
                    @csrf

                    <div id="create-student-error"
                        class="hidden mb-4 p-3 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm"></div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Thuộc Lớp <span
                                    class="text-red-500">*</span></label>
                            <select name="class_id" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                <option value="">-- Chọn Lớp --</option>
                                @foreach ($classes as $cls)
                                    <option value="{{ $cls->id }}">{{ $cls->code }} - {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Mã Sinh Viên <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="student_code" id="create_student_code" required
                                    placeholder="VD: 20110001"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm font-mono uppercase focus:ring-1 focus:ring-primary">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Ngày sinh</label>
                                <input type="date" name="dob"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Họ và Tên <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="fullname" id="create_fullname" required
                                placeholder="VD: Nguyễn Văn A"
                                class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Email Hệ thống</label>
                            <div class="relative">
                                <input type="email" name="email" id="create_email"
                                    placeholder="Tự tạo hoặc nhập thủ công"
                                    class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary pr-10">
                                <span
                                    class="absolute right-3 top-2.5 text-slate-400 material-symbols-outlined !text-[16px]">mail</span>
                            </div>
                            <p class="text-[11px] text-blue-500 mt-1 italic">Hệ thống sẽ tự động tạo email dựa vào tên và
                                mã SV (vd: an20110001@vnkgu.edu.vn).</p>
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Trạng thái <span
                                    class="text-red-500">*</span></label>
                            <select name="status" required
                                class="w-full px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                                <option value="studying" selected>Đang học</option>
                                <option value="reserved">Bảo lưu</option>
                                <option value="dropped">Thôi học</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-6 pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors text-sm font-medium">Hủy
                            bỏ</button>
                        <button type="submit" id="btn-submit-create"
                            class="px-5 py-2 bg-primary text-white rounded-sm hover:bg-primary/90 flex items-center gap-2 text-sm font-medium shadow-sm transition-all active:scale-95">
                            <span class="material-symbols-outlined !text-[18px]">save</span> Thêm Sinh Viên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL UNIVERSAL & LOADING --}}
    @include('admin.classes.partials.universal_confirm_modal')
    {{-- Đã có loading overlay trong table, có thể bỏ partial loading_modal nếu muốn --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. BIẾN ---
            const selectAll = document.getElementById('select-all');
            const btnDeleteSelected = document.getElementById('btn-delete-selected');
            const btnRestoreSelected = document.getElementById('btn-restore-selected');
            const checkboxes = document.querySelectorAll('.student-checkbox');
            const tableLoading = document.getElementById('table-loading');

            // --- 2. XỬ LÝ CHECKBOX HÀNG LOẠT ---
            function toggleActionBtns() {
                const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
                const selectedCount = checkedBoxes.length;

                let hasActive = false;
                let hasTrashed = false;

                checkedBoxes.forEach(cb => {
                    if (cb.dataset.trashed === 'true') hasTrashed = true;
                    else hasActive = true;
                });

                // Reset ẩn hết nút
                btnDeleteSelected.classList.add('hidden');
                btnRestoreSelected.classList.add('hidden');

                if (selectedCount > 0) {
                    if (hasActive && hasTrashed) {
                        // Chọn lẫn lộn -> Không hiện nút nào, có thể hiện Toast cảnh báo nếu muốn
                    } else if (hasTrashed) {
                        // Chỉ chọn những dòng đã ẩn -> Hiện nút Khôi phục
                        btnRestoreSelected.classList.remove('hidden');
                        btnRestoreSelected.innerHTML =
                            `<span class="material-symbols-outlined !text-[18px]">history</span> Khôi phục (${selectedCount})`;
                    } else {
                        // Chỉ chọn những dòng đang hoạt động -> Hiện nút Ẩn
                        btnDeleteSelected.classList.remove('hidden');
                        btnDeleteSelected.innerHTML =
                            `<span class="material-symbols-outlined !text-[18px]">visibility_off</span> Ẩn (${selectedCount})`;
                    }
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    toggleActionBtns();
                });
            }
            checkboxes.forEach(cb => {
                cb.addEventListener('change', toggleActionBtns);
            });

            // --- 3. HÀM GỌI API CHUNG (Dùng fetch + showToast) ---
            async function performAction(url, method, body, successMsg) {
                if (tableLoading) tableLoading.classList.remove('hidden');

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(body)
                    });
                    const data = await response.json();

                    if (data.success) {
                        showToast('success', successMsg || data.message);
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        if (tableLoading) tableLoading.classList.add('hidden');
                        showToast('error', 'Lỗi: ' + data.message);
                    }
                } catch (e) {
                    if (tableLoading) tableLoading.classList.add('hidden');
                    showToast('error', 'Lỗi hệ thống: ' + e.message);
                }
            }

            // --- 4. GẮN SỰ KIỆN NÚT HÀNG LOẠT ---

            // Ẩn (Xóa) Nhiều - Dùng showConfirm với type 'danger'
            if (btnDeleteSelected) {
                btnDeleteSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb =>
                        cb.value);

                    showConfirm(
                        'Ẩn ' + ids.length + ' Sinh Viên?',
                        'Các sinh viên này sẽ bị vô hiệu hóa (không xóa hẳn). Bạn có chắc chắn?',
                        () => {
                            performAction('{{ route('admin.students.bulk_destroy') }}', 'POST', {
                                ids: ids
                            }, 'Đã ẩn thành công!');
                        },
                        'danger' // Màu đỏ
                    );
                });
            }

            // Khôi phục Nhiều - Dùng showConfirm mặc định (màu xanh)
            if (btnRestoreSelected) {
                btnRestoreSelected.addEventListener('click', function() {
                    const ids = Array.from(document.querySelectorAll('.student-checkbox:checked')).map(cb =>
                        cb.value);

                    showConfirm(
                        'Khôi phục ' + ids.length + ' Sinh Viên?',
                        'Các sinh viên này sẽ hoạt động trở lại.',
                        () => {
                            performAction('{{ route('admin.students.bulk_restore') }}', 'POST', {
                                ids: ids
                            }, 'Đã khôi phục thành công!');
                        }
                    );
                });
            }

            // --- 5. GẮN SỰ KIỆN CHO CÁC HÀM TOÀN CỤC (Để gọi từ onclick trong HTML) ---

            window.deleteStudent = function(id, code) {
                showConfirm(
                    'Ẩn Sinh Viên?',
                    `Bạn muốn ẩn sinh viên <b>${code}</b>? <br>Dữ liệu sẽ được chuyển vào mục đã ẩn.`,
                    () => {
                        performAction(`/admin/students/${id}`, 'POST', {
                            _method: 'DELETE'
                        }, 'Đã ẩn sinh viên!');
                    },
                    'danger'
                );
            };

            window.restoreStudent = function(id, code) {
                showConfirm(
                    'Khôi phục Sinh Viên?',
                    `Bạn muốn khôi phục sinh viên <b>${code}</b>?`,
                    () => {
                        performAction(`/admin/students/${id}/restore`, 'POST', {},
                            'Đã khôi phục sinh viên!');
                    }
                );
            };

            // --- 6. AUTO EMAIL GENERATE (Logic cũ cho Modal thêm) ---
            const createFullname = document.getElementById('create_fullname');
            const createCode = document.getElementById('create_student_code');
            const createEmail = document.getElementById('create_email');

            function generateEmailForModal() {
                const fullname = createFullname.value.trim();
                const code = createCode.value.trim().toLowerCase();
                if (!fullname || !code) return;
                let str = fullname.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
                str = str.replace(/đ/g, "d").replace(/Đ/g, "D");
                str = str.trim().toLowerCase();
                const parts = str.split(/\s+/);
                const lastName = parts.pop();
                if (lastName && code) {
                    createEmail.value = `${lastName}${code}@vnkgu.edu.vn`;
                }
            }
            if (createFullname && createCode) {
                createFullname.addEventListener('blur', generateEmailForModal);
                createCode.addEventListener('blur', generateEmailForModal);
            }
        });
    </script>
@endsection
