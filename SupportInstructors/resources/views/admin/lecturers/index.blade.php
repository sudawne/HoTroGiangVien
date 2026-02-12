@extends('layouts.admin')
@section('title', 'Quản lý Giảng viên')

@section('content')

    <div class="w-full px-4 py-6" x-data="{
        confirmModalOpen: false,
        errorModalOpen: false,
        actionType: '',
        selectedCount: 0,
        isSubmitting: false,
    
        // Hàm mở modal xác nhận
        openConfirm(type) {
            this.actionType = type;
            this.selectedCount = document.querySelectorAll('.select-item:checked').length;
            this.confirmModalOpen = true;
        },
    
        // Hàm thực thi AJAX (Nằm trong x-data để dùng được this.isSubmitting)
        executeBulkAction() {
            if (this.isSubmitting) return;
            this.isSubmitting = true;
    
            const ids = Array.from(document.querySelectorAll('.select-item:checked')).map(cb => cb.value);
    
            if (ids.length === 0) {
                this.isSubmitting = false;
                this.confirmModalOpen = false;
                return;
            }
    
            // Chọn route dựa trên actionType
            let url = (this.actionType === 'delete') ?
                '{{ route('admin.lecturers.bulk_delete') }}' :
                '{{ route('admin.lecturers.bulk_restore') }}';
    
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ ids: ids })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra');
                        this.isSubmitting = false;
                    }
                })
                .catch(err => {
                    console.error(err);
                    alert('Lỗi kết nối hệ thống');
                    this.isSubmitting = false;
                });
        }
    }">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Danh sách Giảng viên</h1>
                <p class="text-xs text-slate-500">Quản lý thông tin đội ngũ giảng dạy</p>
            </div>
            <div class="flex items-center gap-2">
                {{-- KHU VỰC NÚT HÀNG LOẠT (JS Vanilla sẽ toggle class hidden ở đây) --}}
                <div id="bulk-restore-btn" class="hidden flex gap-2 bulk-anim">
                    <button @click="openConfirm('restore')"
                        class="h-10 px-3 bg-blue-100 border border-blue-200 text-blue-600 rounded-sm font-medium hover:bg-blue-200 flex items-center gap-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                        <span class="hidden sm:inline text-xs">Hiện/Khôi phục</span>
                    </button>
                </div>

                <div id="bulk-delete-btn" class="hidden flex gap-2 bulk-anim">
                    <button @click="openConfirm('delete')"
                        class="h-10 px-3 bg-red-100 border border-red-200 text-red-600 rounded-sm font-medium hover:bg-red-200 flex items-center gap-2 shadow-sm transition-all">
                        <span class="material-symbols-outlined !text-[20px]">visibility_off</span>
                        <span class="hidden sm:inline text-xs">Ẩn đã chọn</span>
                    </button>
                </div>

                {{-- Nút Thêm mới --}}
                <a href="{{ route('admin.lecturers.create') }}"
                    class="h-10 px-4 bg-primary text-white rounded-sm font-medium hover:bg-primary/90 flex items-center gap-2 shadow-sm transition-colors">
                    <span class="material-symbols-outlined !text-[20px]">add</span>
                    <span class="hidden sm:inline">Thêm mới</span>
                </a>
            </div>
        </div>

        {{-- Bảng danh sách --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                            <th class="px-4 py-3 w-10 text-center">
                                <input type="checkbox" id="select-all"
                                    class="rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                            </th>
                            <th class="px-6 py-3">Giảng viên</th>
                            <th class="px-6 py-3">Mã GV</th>
                            <th class="px-6 py-3">Khoa / Đơn vị</th>
                            <th class="px-6 py-3">Học vị</th>
                            <th class="px-6 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        @forelse($lecturers as $lec)
                            @php $isTrashed = $lec->user && $lec->user->trashed(); @endphp
                            <tr
                                class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800 
                                {{ $isTrashed ? 'deleted-row' : '' }} 
                                {{ session('highlight_id') == $lec->id ? 'highlight-row' : '' }}">

                                <td class="px-4 py-3 text-center">
                                    <input type="checkbox"
                                        class="select-item rounded border-gray-300 text-primary focus:ring-primary cursor-pointer"
                                        value="{{ $lec->id }}" data-trashed="{{ $isTrashed ? 'true' : 'false' }}">
                                </td>
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @if ($lec->user)
                                            <div class="relative">
                                                @if ($lec->user->avatar_url)
                                                    <img src="{{ asset('storage/' . $lec->user->avatar_url) }}"
                                                        class="size-9 rounded-full object-cover border border-slate-200">
                                                @else
                                                    <div
                                                        class="size-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold uppercase text-xs">
                                                        {{ substr($lec->user->name, 0, 1) }}
                                                    </div>
                                                @endif
                                                @if ($isTrashed)
                                                    <div class="absolute -bottom-1 -right-1 bg-gray-500 text-white rounded-full p-0.5"
                                                        title="Đã ẩn">
                                                        <span
                                                            class="material-symbols-outlined !text-[10px] block">visibility_off</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-700 dark:text-white">
                                                    {{ $lec->user->name }}
                                                    @if ($isTrashed)
                                                        <span class="text-[10px] text-red-500 font-normal ml-1">(Đã
                                                            ẩn)</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-slate-500">{{ $lec->user->email }}</p>
                                            </div>
                                        @else
                                            <div
                                                class="size-9 rounded-full bg-red-100 flex items-center justify-center text-red-500">
                                                <span class="material-symbols-outlined !text-[18px]">error</span>
                                            </div>
                                            <span class="text-red-500 italic text-xs">Lỗi User ID</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-3 font-mono text-slate-600">{{ $lec->lecturer_code }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $lec->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3">
                                    <span
                                        class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium border border-blue-100">{{ $lec->degree ?? 'N/A' }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if ($isTrashed)
                                            {{-- Form Khôi phục --}}
                                            <form action="{{ route('admin.lecturers.restore', $lec->id) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                                    title="Hiện/Khôi phục">
                                                    <span class="material-symbols-outlined !text-[18px]">visibility</span>
                                                </button>
                                            </form>
                                        @else
                                            {{-- Nút Sửa --}}
                                            <a href="{{ route('admin.lecturers.edit', $lec->id) }}"
                                                class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors"><span
                                                    class="material-symbols-outlined !text-[18px]">edit</span></a>
                                            {{-- Form Ẩn --}}
                                            <form action="{{ route('admin.lecturers.destroy', $lec->id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn ẩn giảng viên này?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                                    title="Ẩn giảng viên">
                                                    <span
                                                        class="material-symbols-outlined !text-[18px]">visibility_off</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Chưa có dữ liệu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">{{ $lecturers->links() }}</div>
        </div>

        {{-- ================= MODAL XÁC NHẬN (Confirm) ================= --}}
        <div x-show="confirmModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div x-show="confirmModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>

            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="confirmModalOpen" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full sm:mx-0 sm:h-10 sm:w-10"
                                :class="actionType === 'delete' ? 'bg-red-100' : 'bg-blue-100'">
                                <span class="material-symbols-outlined"
                                    :class="actionType === 'delete' ? 'text-red-600' : 'text-blue-600'"
                                    x-text="actionType === 'delete' ? 'visibility_off' : 'visibility'"></span>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900"
                                    x-text="actionType === 'delete' ? 'Xác nhận Ẩn' : 'Xác nhận Khôi phục'"></h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Bạn có chắc chắn muốn <span
                                            x-text="actionType === 'delete' ? 'ẩn' : 'khôi phục'"></span>
                                        <span x-text="selectedCount" class="font-bold text-slate-800"></span> giảng viên
                                        đã chọn?
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        {{-- QUAN TRỌNG: Gọi executeBulkAction() --}}
                        <button type="button" @click="executeBulkAction()" :disabled="isSubmitting"
                            class="inline-flex w-full justify-center rounded-md px-3 py-2 text-sm font-semibold text-white shadow-sm sm:ml-3 sm:w-auto transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                            :class="actionType === 'delete' ? 'bg-red-600 hover:bg-red-500' : 'bg-blue-600 hover:bg-blue-500'">
                            <span x-show="!isSubmitting">Đồng ý</span>
                            <span x-show="isSubmitting">Đang xử lý...</span>
                        </button>
                        <button type="button" @click="confirmModalOpen = false" :disabled="isSubmitting"
                            class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">
                            Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ================= MODAL LỖI (Error) ================= --}}
        <div x-show="errorModalOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md">
                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-outlined text-orange-600">warning</span>
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">Lỗi chọn dữ liệu</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Vui lòng chọn cùng một loại trạng thái (hoặc cùng
                                        <b>đã ẩn</b>, hoặc cùng <b>đang hoạt động</b>) để thao tác.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" @click="errorModalOpen = false"
                            class="inline-flex w-full justify-center rounded-md bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:w-auto">Đã
                            hiểu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // --- LOGIC JS THƯỜNG (Xử lý UI Checkbox) ---
        const selectAll = document.getElementById('select-all');
        const items = document.querySelectorAll('.select-item');
        const btnRestore = document.getElementById('bulk-restore-btn');
        const btnDelete = document.getElementById('bulk-delete-btn');

        function toggleBulkActions() {
            const checkedItems = document.querySelectorAll('.select-item:checked');
            let hasTrashed = false;
            let hasActive = false;

            checkedItems.forEach(item => {
                if (item.dataset.trashed === 'true') hasTrashed = true;
                else hasActive = true;
            });

            btnRestore.classList.add('hidden');
            btnDelete.classList.add('hidden');

            // Logic hiển thị nút
            if (hasActive && hasTrashed) {
                // Không hiện nút nào nếu chọn lộn xộn
            } else if (hasActive) {
                btnDelete.classList.remove('hidden');
            } else if (hasTrashed) {
                btnRestore.classList.remove('hidden');
            }
        }

        selectAll.addEventListener('change', function() {
            items.forEach(item => item.checked = this.checked);
            checkMixedSelection();
            toggleBulkActions();
        });

        items.forEach(item => {
            item.addEventListener('change', () => {
                checkMixedSelection(item);
                toggleBulkActions();
            });
        });

        function checkMixedSelection(currentItem = null) {
            const checkedItems = document.querySelectorAll('.select-item:checked');
            let hasTrashed = false;
            let hasActive = false;
            checkedItems.forEach(i => {
                if (i.dataset.trashed === 'true') hasTrashed = true;
                else hasActive = true;
            });

            if (hasTrashed && hasActive) {
                // Bật modal lỗi bằng cách gán giá trị cho Alpine data thông qua DOM
                document.querySelector('[x-data]').__x.$data.errorModalOpen = true;

                if (currentItem) currentItem.checked = false;
                else {
                    selectAll.checked = false;
                    items.forEach(i => i.checked = false);
                }
            }
        }
    </script>
@endsection
