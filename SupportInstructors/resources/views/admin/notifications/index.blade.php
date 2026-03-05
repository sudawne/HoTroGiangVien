@extends('layouts.admin')
@section('title', 'Quản lý Thông báo')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-primary !text-[36px]">campaign</span>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Quản lý Thông báo</h1>
                    <p class="text-sm text-slate-500 mt-1">Quản lý, xét duyệt thông báo của Khoa và Giảng viên</p>
                </div>
            </div>
            <a href="{{ route('admin.notifications.create') }}"
                class="px-5 py-2.5 bg-primary text-white font-bold rounded-sm hover:bg-primary/90 flex items-center gap-2 shadow-sm transition-colors">
                <span class="material-symbols-outlined !text-[20px]">add_circle</span> Đăng bài mới
            </a>
        </div>

        <div class="bg-white border border-slate-200 rounded-sm shadow-sm mb-6">
            <form id="filterForm" action="{{ route('admin.notifications.index') }}" method="GET">
                <input type="hidden" name="role_filter" id="role_filter" value="{{ $roleFilter }}">
                <input type="hidden" name="status_filter" id="status_filter" value="{{ $statusFilter }}">

                <div class="flex border-b border-slate-100 px-2 overflow-x-auto">
                    <button type="button" onclick="applyFilter('role_filter', 'all')"
                        class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap {{ $roleFilter == 'all' ? 'border-primary text-primary' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        Tất cả thông báo
                    </button>
                    <button type="button" onclick="applyFilter('role_filter', 'admin')"
                        class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap flex items-center gap-1 {{ $roleFilter == 'admin' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <span class="material-symbols-outlined !text-[16px]">admin_panel_settings</span> Của Ban Chủ Nhiệm
                        Khoa
                    </button>
                    <button type="button" onclick="applyFilter('role_filter', 'lecturer')"
                        class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap flex items-center gap-1 {{ $roleFilter == 'lecturer' ? 'border-green-600 text-green-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <span class="material-symbols-outlined !text-[16px]">school</span> Của Cố vấn học tập
                    </button>
                </div>

                <div class="p-4 bg-slate-50/50 flex flex-col lg:flex-row justify-between gap-4 items-center">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="applyFilter('status_filter', 'all')"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-colors border {{ $statusFilter == 'all' ? 'bg-slate-800 text-white border-slate-800' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                            Tất cả trạng thái
                        </button>
                        <button type="button" onclick="applyFilter('status_filter', 'pending')"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-colors border flex items-center gap-1 {{ $statusFilter == 'pending' ? 'bg-yellow-100 text-yellow-700 border-yellow-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined !text-[14px]">hourglass_empty</span> Cần xét duyệt
                        </button>
                        <button type="button" onclick="applyFilter('status_filter', 'approved')"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-colors border flex items-center gap-1 {{ $statusFilter == 'approved' ? 'bg-green-100 text-green-700 border-green-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined !text-[14px]">check_circle</span> Đã xuất bản
                        </button>
                        <button type="button" onclick="applyFilter('status_filter', 'draft')"
                            class="px-3 py-1.5 rounded-full text-xs font-bold transition-colors border flex items-center gap-1 {{ $statusFilter == 'draft' ? 'bg-slate-200 text-slate-700 border-slate-300' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined !text-[14px]">draft</span> Bản nháp
                        </button>
                    </div>

                    <div class="flex items-center gap-2 w-full lg:w-auto">
                        <label class="text-sm font-bold text-slate-600 whitespace-nowrap">Lọc theo Lớp:</label>
                        <select name="class_id" onchange="document.getElementById('filterForm').submit()"
                            class="w-full lg:w-64 px-3 py-2 border border-slate-300 rounded-sm text-sm focus:ring-1 focus:ring-primary">
                            <option value="">-- Toàn bộ hệ thống --</option>
                            @foreach ($classes as $c)
                                <option value="{{ $c->id }}" {{ request('class_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->code }} - {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Tiêu đề / Thời gian</th>
                            <th class="px-6 py-4">Phạm vi gửi</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4">Người đăng</th>
                            <th class="px-6 py-4 text-right min-w-[180px]">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($notifications as $notify)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.notifications.show', $notify->id) }}"
                                        class="font-bold text-slate-800 text-base hover:text-primary mb-1 block leading-tight">
                                        {{ $notify->title }}
                                    </a>
                                    <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-slate-500">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined !text-[14px]">schedule</span>
                                            {{ $notify->created_at->format('d/m/Y H:i') }}
                                        </span>
                                        @if ($notify->attachment_url)
                                            <span
                                                class="text-blue-600 flex items-center gap-1 bg-blue-50 px-1.5 rounded font-medium border border-blue-100">
                                                <span class="material-symbols-outlined !text-[14px]">attach_file</span> Đính
                                                kèm
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    @if ($notify->target_audience == 'all')
                                        <span
                                            class="font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded text-xs border border-slate-200">Toàn
                                            trường</span>
                                    @else
                                        <span
                                            class="font-bold text-green-700 bg-green-50 px-2 py-1 rounded text-xs border border-green-200">Lớp
                                            {{ $notify->class->code ?? 'N/A' }}</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    @if ($notify->status == 'draft')
                                        <span
                                            class="px-2 py-1 bg-slate-100 text-slate-600 text-xs font-bold rounded border border-slate-200">Bản
                                            nháp</span>
                                    @elseif($notify->status == 'pending')
                                        <span
                                            class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded border border-yellow-300 shadow-sm animate-pulse">Chờ
                                            duyệt</span>
                                    @else
                                        <span
                                            class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded border border-green-300">Đã
                                            xuất bản</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs shadow-sm {{ $notify->sender->role_id == 1 ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                                            {{ substr($notify->sender->name ?? 'A', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-700 leading-tight">
                                                {{ $notify->sender->name ?? 'N/A' }}</p>
                                            <p
                                                class="text-[10px] font-medium mt-0.5 {{ $notify->sender->role_id == 1 ? 'text-blue-500' : 'text-green-600' }}">
                                                {{ $notify->sender->role_id == 1 ? 'ADMIN' : 'GIẢNG VIÊN' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (in_array($notify->status, ['draft', 'pending']))
                                            <a href="{{ route('admin.notifications.edit', $notify->id) }}"
                                                class="px-2 py-1.5 bg-blue-50 text-blue-600 border border-blue-200 rounded text-xs font-bold hover:bg-blue-600 hover:text-white transition-colors"
                                                title="Chỉnh sửa">
                                                Sửa
                                            </a>
                                        @endif

                                        @if ($notify->status == 'pending' && Auth::user()->role_id == 1)
                                            <form id="approve-form-{{ $notify->id }}"
                                                action="{{ route('admin.notifications.approve', $notify->id) }}"
                                                method="POST" class="hidden">
                                                @csrf
                                            </form>
                                            <button type="button"
                                                onclick="showConfirm('Xác nhận Duyệt bài', 'Hệ thống sẽ ngay lập tức xuất bản thông báo và GỬI EMAIL tự động đến sinh viên. Bạn có chắc chắn?', () => document.getElementById('approve-form-{{ $notify->id }}').submit(), 'primary')"
                                                class="px-2 py-1.5 bg-green-600 text-white rounded text-xs font-bold hover:bg-green-700 shadow-sm transition-colors"
                                                title="Duyệt & Xuất bản">
                                                Duyệt
                                            </button>
                                        @endif

                                        <form id="delete-form-{{ $notify->id }}"
                                            action="{{ route('admin.notifications.destroy', $notify->id) }}"
                                            method="POST" class="hidden">
                                            @csrf @method('DELETE')
                                        </form>
                                        <button type="button"
                                            onclick="showConfirm('Xóa thông báo', 'Bạn có chắc chắn muốn xóa bài đăng này? Hành động này không thể hoàn tác.', () => document.getElementById('delete-form-{{ $notify->id }}').submit(), 'danger')"
                                            class="px-2 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded text-xs font-bold hover:bg-red-600 hover:text-white transition-colors"
                                            title="Xóa bỏ">
                                            Xóa
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12">
                                    <span
                                        class="material-symbols-outlined text-[48px] text-slate-300 mb-3 block">inventory_2</span>
                                    <p class="text-slate-500 font-medium">Chưa có dữ liệu phù hợp với bộ lọc hiện tại.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>

    @include('admin.classes.partials.universal_confirm_modal')

    <script>
        function applyFilter(key, value) {
            document.getElementById(key).value = value;
            document.getElementById('filterForm').submit();
        }

        function showConfirm(title, message, callback, type = 'primary') {
            const modal = document.getElementById('universalModal');
            document.getElementById('uni-modal-title').innerText = title;
            document.getElementById('uni-modal-desc').innerHTML = message;

            const btnConfirm = document.getElementById('btn-uni-confirm');
            btnConfirm.onclick = () => {
                modal.classList.add('hidden');
                callback();
            };

            const icon = document.getElementById('uni-modal-icon');
            const iconBg = document.getElementById('uni-modal-icon-bg');

            if (type === 'danger') {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-red-600 hover:bg-red-700 flex items-center gap-2";
                icon.innerText = 'warning';
                icon.className = 'material-symbols-outlined text-[24px] text-red-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100';
            } else {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-blue-600 hover:bg-blue-700 flex items-center gap-2";
                icon.innerText = 'help';
                icon.className = 'material-symbols-outlined text-[24px] text-blue-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100';
            }

            modal.classList.remove('hidden');
            modal.removeAttribute('style');
        }
    </script>
@endsection
