@extends('layouts.admin')
@section('title', 'Danh sách Xóa học phần')

@section('content')
<div class="w-full px-4 py-6" x-data="{ showImportModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Sinh viên bị Xóa học phần</h1>
            <p class="text-slate-500 text-sm mt-1">Theo dõi các sinh viên bị hủy môn do nợ học phí hoặc lý do khác.</p>
        </div>
        <button @click="showImportModal = true" 
            class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-lg hover:bg-green-700 transition-colors shadow-lg shadow-green-600/30">
            <span class="material-symbols-outlined !text-[20px]">upload_file</span> Import Excel
        </button>
    </div>

    {{-- TOOLBAR --}}
    <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm mb-6 p-4">
        <form method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Tìm tên hoặc MSSV..." 
                    class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-1 focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                <span class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[18px]">search</span>
            </div>
            <div class="w-full md:w-64">
                <select name="semester_id" onchange="this.form.submit()" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-primary dark:bg-slate-800 dark:border-slate-600 dark:text-white">
                    <option value="">-- Tất cả học kỳ --</option>
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}" {{ request('semester_id') == $sem->id ? 'selected' : '' }}>
                            {{ $sem->name }} ({{ $sem->academic_year }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                    <tr>
                        <th class="px-6 py-3">Sinh viên</th>
                        <th class="px-6 py-3">Lớp</th>
                        <th class="px-6 py-3">Môn bị xóa</th>
                        <th class="px-6 py-3">Học kỳ</th>
                        <th class="px-6 py-3">Lý do / Số tiền</th>
                        <th class="px-6 py-3 text-right">Tác vụ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse($cancellations as $item)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800 dark:text-white">{{ $item->student->fullname ?? 'N/A' }}</div>
                                <div class="text-xs text-primary font-mono">{{ $item->student->student_code ?? '---' }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->student->studentClass->code ?? '' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-700 dark:text-slate-300">{{ $item->subject_name }}</div>
                                <div class="text-xs text-slate-500 font-mono">{{ $item->subject_code }}</div>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $item->semester->name ?? '' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-red-50 text-red-600 border border-red-100 rounded text-xs font-bold">
                                    {{ $item->reason }}
                                </span>
                                @if($item->debt_amount > 0)
                                    <div class="text-xs text-slate-500 mt-1">Nợ: {{ number_format($item->debt_amount) }}đ</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.course_cancellations.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Xóa bản ghi này?')">
                                    @csrf @method('DELETE')
                                    <button class="text-slate-400 hover:text-red-600 transition-colors">
                                        <span class="material-symbols-outlined !text-[18px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-slate-500 italic">Chưa có dữ liệu xóa học phần nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
            {{ $cancellations->links() }}
        </div>
    </div>

    {{-- MODAL IMPORT --}}
    <div x-show="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" x-cloak style="display: none;">
        <div class="bg-white dark:bg-[#1e1e2d] w-full max-w-md rounded-lg shadow-xl overflow-hidden" @click.away="showImportModal = false">
            <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">Import Excel Xóa HP</h3>
                <button @click="showImportModal = false" class="text-slate-400 hover:text-red-500"><span class="material-symbols-outlined">close</span></button>
            </div>
            <form action="{{ route('admin.course_cancellations.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Chọn Học kỳ áp dụng <span class="text-red-500">*</span></label>
                    <select name="semester_id" required class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name }} ({{ $sem->academic_year }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">File Excel/CSV <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>
                <div class="flex justify-end pt-2">
                    <button type="button" @click="showImportModal = false" class="px-4 py-2 border border-slate-300 rounded-md mr-2 hover:bg-slate-50">Hủy</button>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-primary/90">Tải lên</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection