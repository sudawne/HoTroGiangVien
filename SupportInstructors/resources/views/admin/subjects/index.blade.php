@extends('layouts.admin')
@section('title', 'Quản lý Môn học')

@section('content')
    <div class="w-full px-4 py-6" x-data="{ showCreateModal: false, showEditModal: false }">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Quản lý Môn học</h1>
                <p class="text-slate-500 text-sm mt-1">Danh sách các học phần trong chương trình đào tạo.</p>
            </div>
            <button @click="showCreateModal = true"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined !text-[20px]">add</span> Thêm môn học
            </button>
        </div>

        {{-- TOOLBAR & TABLE --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm">
            {{-- Search Bar --}}
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-center gap-4">
                <form method="GET" class="relative w-full md:w-80">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm mã môn hoặc tên môn..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                    <span
                        class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[18px]">search</span>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">STT</th>
                            <th class="px-6 py-3 w-32">Mã Môn</th>
                            <th class="px-6 py-3">Tên Môn Học</th>
                            <th class="px-6 py-3 w-32 text-center">Số TC</th>
                            <th class="px-6 py-3 w-32 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($subjects as $index => $subject)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-4 text-center text-slate-500">
                                    {{ $subjects->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-primary">
                                    {{ $subject->code }}
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-800 dark:text-slate-200">
                                    {{ $subject->name }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="px-2 py-1 bg-slate-100 text-slate-600 rounded text-xs font-bold border border-slate-200">
                                        {{ $subject->credits }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Nút Sửa --}}
                                        <button type="button"
                                            @click="openEditModal('{{ $subject->id }}', '{{ $subject->code }}', '{{ $subject->name }}', '{{ $subject->credits }}')"
                                            class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-100 transition-colors"
                                            title="Sửa">
                                            <span class="material-symbols-outlined !text-[18px]">edit</span>
                                        </button>

                                        {{-- Nút Xóa --}}
                                        <form action="{{ route($routePrefix . 'subjects.destroy', $subject->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa môn {{ $subject->code }}?');">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 bg-red-50 text-red-600 rounded hover:bg-red-100 transition-colors"
                                                title="Xóa">
                                                <span class="material-symbols-outlined !text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">
                                    Không tìm thấy môn học nào.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $subjects->links() }}
            </div>
        </div>

        {{-- MODAL THÊM MỚI --}}
        <div x-show="showCreateModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak
            style="display: none;">
            <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-md rounded-lg shadow-xl overflow-hidden"
                @click.away="showCreateModal = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800">Thêm Môn Học Mới</h3>
                    <button @click="showCreateModal = false" class="text-slate-400 hover:text-red-500"><span
                            class="material-symbols-outlined">close</span></button>
                </div>
                <form action="{{ route($routePrefix . 'subjects.store') }}" method="POST" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mã môn học <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="code" required placeholder="VD: IT001"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên môn học <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" required placeholder="VD: Lập trình web"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Số tín chỉ <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="credits" required min="0" value="3"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" @click="showCreateModal = false"
                            class="px-4 py-2 border border-slate-300 rounded-md mr-2 hover:bg-slate-50">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90">Lưu
                            lại</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL CẬP NHẬT --}}
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak
            style="display: none;">
            <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-md rounded-lg shadow-xl overflow-hidden"
                @click.away="showEditModal = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800">Cập nhật Môn Học</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-red-500"><span
                            class="material-symbols-outlined">close</span></button>
                </div>
                <form id="editForm" method="POST" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Mã môn học <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="code" id="edit_code" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Tên môn học <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="name" id="edit_name" required
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Số tín chỉ <span
                                class="text-red-500">*</span></label>
                        <input type="number" name="credits" id="edit_credits" required min="0"
                            class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary">
                    </div>
                    <div class="flex justify-end pt-2">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 border border-slate-300 rounded-md mr-2 hover:bg-slate-50">Hủy</button>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90">Cập
                            nhật</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <script>
        function openEditModal(id, code, name, credits) {
            // Cập nhật action form
            document.getElementById('editForm').action = `/admin/subjects/${id}`;
            // Fill dữ liệu
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_credits').value = credits;

            // Mở modal (AlpineJS sẽ bắt sự kiện này nếu dùng window dispatch, 
            // nhưng ở đây ta dùng x-data local nên dispatch event hoặc set trực tiếp thông qua cơ chế khác.
            // Tuy nhiên, vì x-data nằm ở div cha, ta cần truy cập nó. 
            // Cách đơn giản nhất trong blade thuần kết hợp Alpine là dùng $dispatch hoặc gán vào window object).

            // Cách fix nhanh nhất cho Alpine inline:
            document.querySelector('[x-data]').__x.$data.showEditModal = true;
        }
    </script>
@endsection
