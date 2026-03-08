@extends('layouts.admin')
@section('title', 'Import Xóa Học Phần')

@section('content')
<div class="w-full max-w-2xl mx-auto px-4 py-8">
    <div class="bg-white dark:bg-[#1e1e2d] rounded-lg shadow-md border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800">
            <h2 class="text-xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                <span class="material-symbols-outlined text-green-600">upload_file</span> Import Dữ liệu Excel
            </h2>
            <p class="text-sm text-slate-500 mt-1">Tải lên danh sách sinh viên bị xóa học phần (File Excel/CSV)</p>
        </div>
        
        <form action="{{ route('admin.course_cancellations.preview') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">1. Chọn Học kỳ áp dụng</label>
                <select name="semester_id" class="w-full px-3 py-2 border border-slate-300 rounded-md focus:ring-primary focus:border-primary bg-white dark:bg-slate-800">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->name }} ({{ $sem->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">2. Chọn File dữ liệu</label>
                <div class="border-2 border-dashed border-slate-300 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors relative">
                    <input type="file" name="file" required accept=".xlsx,.xls,.csv" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <span class="material-symbols-outlined text-4xl text-slate-400 mb-2">cloud_upload</span>
                    <p class="text-sm text-slate-600 font-medium">Kéo thả hoặc click để chọn file</p>
                    <p class="text-xs text-slate-400 mt-1">Hỗ trợ .xlsx, .xls, .csv</p>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <a href="{{ route('admin.course_cancellations.index') }}" class="px-4 py-2 border rounded-md hover:bg-slate-50 text-slate-700">Quay lại</a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded-md hover:bg-primary/90 font-medium shadow-sm">
                    Tiếp tục (Xem trước)
                </button>
            </div>
        </form>
    </div>
</div>
@endsection