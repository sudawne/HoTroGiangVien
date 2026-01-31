@extends('layouts.admin')
@section('title', 'Import Cảnh Báo Học Tập')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Import Cảnh Báo Học Tập</h1>
        <p class="text-slate-500 text-sm">Tải lên file Excel (.xlsx, .csv) chứa danh sách sinh viên bị cảnh báo.</p>
    </div>

    <div class="bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 p-6">
        <form action="{{ route('admin.academic_warnings.preview') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Chọn Học Kỳ --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Chọn Học Kỳ & Năm Học</label>
                <select name="semester_id" class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-lg p-2.5 focus:ring-primary focus:border-primary">
                    @foreach($semesters as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->name }} ({{ $sem->academic_year }})</option>
                    @endforeach
                </select>
            </div>

            {{-- Upload File --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">File Dữ Liệu</label>
                <div class="flex items-center justify-center w-full">
                    <label for="dropzone-file" id="dropzone-label" class="flex flex-col items-center justify-center w-full h-64 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 dark:hover:bg-bray-800 dark:bg-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:hover:border-slate-500 dark:hover:bg-slate-600 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" id="dropzone-content">
                            {{-- Icon --}}
                            <span class="material-symbols-outlined text-4xl text-slate-400 mb-3" id="upload-icon">cloud_upload</span>
                            
                            {{-- Text chính (Đã thêm ID để JS gọi) --}}
                            <p class="mb-2 text-sm text-slate-500 dark:text-slate-400 text-center" id="file-name-display">
                                <span class="font-semibold">Click để tải lên</span> hoặc kéo thả
                            </p>
                            
                            {{-- Text phụ --}}
                            <p class="text-xs text-slate-500 dark:text-slate-400" id="file-size-display">XLSX, CSV (MAX. 5MB)</p>
                        </div>
                        <input id="dropzone-file" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required />
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.academic_warnings.index') }}" class="px-5 py-2.5 rounded-lg text-slate-600 hover:bg-slate-100 font-medium text-sm transition-colors">Hủy bỏ</a>
                <button type="submit" class="bg-primary hover:bg-indigo-700 text-white px-5 py-2.5 rounded-lg font-medium text-sm transition-colors shadow-lg shadow-indigo-500/30">
                    Xem trước dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT XỬ LÝ HIỂN THỊ TÊN FILE --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('dropzone-file');
        const fileNameDisplay = document.getElementById('file-name-display');
        const fileSizeDisplay = document.getElementById('file-size-display');
        const uploadIcon = document.getElementById('upload-icon');
        const dropzoneLabel = document.getElementById('dropzone-label');

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // 1. Hiển thị tên file
                fileNameDisplay.innerHTML = `<span class="font-bold text-primary">${file.name}</span>`;
                
                // 2. Hiển thị dung lượng (KB/MB)
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                fileSizeDisplay.textContent = `Dung lượng: ${fileSize} MB`;

                // 3. Đổi icon thành file
                uploadIcon.textContent = 'description'; 
                uploadIcon.classList.remove('text-slate-400');
                uploadIcon.classList.add('text-primary');

                // 4. Đổi màu viền để báo hiệu đã chọn
                dropzoneLabel.classList.add('border-primary', 'bg-blue-50', 'dark:bg-slate-800');
                dropzoneLabel.classList.remove('border-slate-300', 'bg-slate-50');
            } else {
                // Reset về ban đầu nếu user hủy chọn
                fileNameDisplay.innerHTML = `<span class="font-semibold">Click để tải lên</span> hoặc kéo thả`;
                fileSizeDisplay.textContent = 'XLSX, CSV (MAX. 5MB)';
                uploadIcon.textContent = 'cloud_upload';
                uploadIcon.classList.add('text-slate-400');
                uploadIcon.classList.remove('text-primary');
                dropzoneLabel.classList.remove('border-primary', 'bg-blue-50', 'dark:bg-slate-800');
                dropzoneLabel.classList.add('border-slate-300', 'bg-slate-50');
            }
        });
    });
</script>
@endsection