@extends('layouts.admin')
@section('title', 'Import Kết Quả Học Tập')

@section('content')
<div class="w-full px-4 py-4 h-[calc(100vh-80px)] flex flex-col">
    
    <form action="{{ route('admin.academic_results.preview') }}" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
        @csrf
        
        {{-- Toolbar --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm mb-4 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.academic_results.index') }}" class="p-2 hover:bg-slate-100 rounded-full text-slate-500">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-white">Import Kết Quả Học Tập</h1>
                    <p class="text-xs text-slate-500">Nhập GPA và Xếp loại từ file Excel</p>
                </div>
            </div>
            <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-sm font-bold text-sm shadow-sm flex items-center gap-2 transition-all">
                <span class="material-symbols-outlined text-[18px]">visibility</span> Xem trước dữ liệu
            </button>
        </div>

        {{-- Upload Area --}}
        <div class="flex-1 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg p-8 shadow-sm flex flex-col items-center justify-center border-dashed border-2 border-slate-300 hover:border-primary/50 transition-colors">
            
            <div class="w-full max-w-lg space-y-4 mb-8">
                <div>
                    <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1">Chọn Học kỳ nhập điểm</label>
                    <select name="semester_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm py-2.5 pl-3" required>
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->name }} ({{ $sem->academic_year }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="text-center w-full max-w-lg border-t dark:border-slate-700 pt-8">
                <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-4">Tải file Excel</label>
                <input type="file" name="file" id="fileInput" class="hidden" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)">
                <label for="fileInput" class="cursor-pointer bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-3 rounded-sm font-bold text-sm hover:bg-emerald-100 transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">upload_file</span>
                    Chọn file từ máy tính
                </label>
                <p id="fileNameDisplay" class="mt-4 text-sm font-medium text-emerald-600 hidden"></p>
            </div>
        </div>
    </form>
</div>

<script>
    function updateFileName(input) {
        const display = document.getElementById('fileNameDisplay');
        if (input.files && input.files[0]) {
            display.textContent = 'Đã chọn: ' + input.files[0].name;
            display.classList.remove('hidden');
        }
    }
</script>
@endsection