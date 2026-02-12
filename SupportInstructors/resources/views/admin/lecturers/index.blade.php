@extends('layouts.admin')
@section('title', 'Quản lý Giảng viên')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Danh sách Giảng viên</h1>
                <p class="text-xs text-slate-500">Quản lý thông tin đội ngũ giảng dạy</p>
            </div>
            <a href="{{ route('admin.lecturers.create') }}"
                class="px-4 py-2 bg-primary text-white rounded-sm font-medium hover:bg-primary/90 flex items-center gap-2 shadow-sm">
                <span class="material-symbols-outlined !text-[18px]">add</span> Thêm mới
            </a>
        </div>

        {{-- Bảng danh sách --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-semibold">
                            <th class="px-6 py-3">Giảng viên</th>
                            <th class="px-6 py-3">Mã GV</th>
                            <th class="px-6 py-3">Khoa / Đơn vị</th>
                            <th class="px-6 py-3">Học vị</th>
                            <th class="px-6 py-3 text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        @forelse($lecturers as $lec)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        {{-- HIỂN THỊ ẢNH AVATAR --}}
                                        @if ($lec->user->avatar_url)
                                            <img src="{{ asset('storage/' . $lec->user->avatar_url) }}"
                                                alt="{{ $lec->user->name }}"
                                                class="size-9 rounded-full object-cover border border-slate-200">
                                        @else
                                            {{-- Placeholder cũ --}}
                                            <div
                                                class="size-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 font-bold uppercase text-xs">
                                                {{ substr($lec->user->name, 0, 1) }}
                                            </div>
                                        @endif

                                        <div>
                                            <p class="font-bold text-slate-700 dark:text-white">{{ $lec->user->name }}</p>
                                            <p class="text-xs text-slate-500">{{ $lec->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3 font-mono text-slate-600">{{ $lec->lecturer_code }}</td>
                                <td class="px-6 py-3 text-slate-600">{{ $lec->department->name ?? 'N/A' }}</td>
                                <td class="px-6 py-3">
                                    <span
                                        class="px-2 py-1 bg-blue-50 text-blue-700 rounded text-xs font-medium border border-blue-100">
                                        {{ $lec->degree ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.lecturers.edit', $lec->id) }}"
                                            class="p-1.5 text-slate-500 hover:text-primary hover:bg-slate-100 rounded transition-colors">
                                            <span class="material-symbols-outlined !text-[18px]">edit</span>
                                        </a>
                                        <form action="{{ route('admin.lecturers.destroy', $lec->id) }}" method="POST"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa giảng viên này?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded transition-colors">
                                                <span class="material-symbols-outlined !text-[18px]">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 italic">Chưa có dữ liệu giảng
                                    viên.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                {{ $lecturers->links() }}
            </div>
        </div>
    </div>
@endsection
