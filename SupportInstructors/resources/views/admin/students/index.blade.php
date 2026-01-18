@extends('layouts.admin')
@section('title', 'Danh sách Sinh viên')

@section('content')
    <div class="w-full px-4 py-6" x-data="{ showImportModal: false }">

        {{-- Header & Toolbar --}}
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Hồ sơ Sinh viên</h1>
                <p class="text-xs text-slate-500">Quản lý thông tin và trạng thái học tập</p>
            </div>

            <div class="flex gap-2">
                <button @click="showImportModal = true"
                    class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">upload_file</span> Import Excel
                </button>
                <a href="{{ route('admin.students.create') }}"
                    class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-sm hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px]">add</span> Thêm mới
                </a>
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
                    class="px-6 py-2 bg-slate-800 text-white text-sm font-semibold rounded-sm hover:bg-slate-700">
                    Lọc dữ liệu
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3">MSSV</th>
                            <th class="px-6 py-3">Họ và Tên</th>
                            <th class="px-6 py-3">Lớp</th>
                            <th class="px-6 py-3">Ngày sinh</th>
                            <th class="px-6 py-3">Trạng thái</th>
                            <th class="px-6 py-3 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $st)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 font-mono font-medium text-primary">{{ $st->student_code }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800 dark:text-white">
                                    {{ $st->fullname }}
                                </td>
                                <td class="px-6 py-3">{{ $st->class->code ?? '---' }}</td>
                                <td class="px-6 py-3 text-slate-500">
                                    {{ $st->dob ? \Carbon\Carbon::parse($st->dob)->format('d/m/Y') : '--' }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($st->status == 'studying')
                                        <span
                                            class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700">ĐANG
                                            HỌC</span>
                                    @elseif($st->status == 'dropped')
                                        <span
                                            class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-red-100 text-red-700">THÔI
                                            HỌC</span>
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-700">{{ Str::upper($st->status) }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <a href="{{ route('admin.students.show', $st->id) }}"
                                        class="text-primary hover:underline font-medium text-xs">Chi tiết</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Không tìm thấy sinh
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

        {{-- MODAL IMPORT (Sử dụng AlpineJS) --}}
        <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50" x-cloak>
            <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-md rounded-lg shadow-xl overflow-hidden"
                @click.away="showImportModal = false">
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-slate-800">Import Sinh viên từ Excel</h3>
                    <button @click="showImportModal = false" class="text-slate-400 hover:text-red-500">
                        <span class="material-symbols-outlined">close</span>
                    </button>
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
                            class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2 file:px-4
                        file:rounded-sm file:border-0
                        file:text-sm file:font-semibold
                        file:bg-primary/10 file:text-primary
                        hover:file:bg-primary/20
                    " />
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

    </div>
@endsection
