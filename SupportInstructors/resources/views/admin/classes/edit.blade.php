@extends('layouts.admin')
@section('title', 'Cập nhật Lớp học')

@section('content')
    <div class="w-full px-4 py-6">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[20px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Cập nhật Lớp học</h1>
                    <p class="text-xs text-slate-500">Chỉnh sửa thông tin lớp {{ $class->code }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[20px]">edit_square</span> Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST" enctype="multipart/form-data"
                class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $class->code) }}" required
                            class="w-full pl-3 pr-3 py-2.5 border {{ $errors->has('code') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-primary focus:ring-primary' }} rounded-sm focus:ring-1 transition-colors font-mono uppercase text-sm">
                        @error('code')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required
                            class="w-full px-3 py-2.5 border {{ $errors->has('academic_year') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-primary focus:ring-primary' }} rounded-sm focus:ring-1 transition-colors text-sm">
                        @error('academic_year')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                            class="w-full px-3 py-2.5 border {{ $errors->has('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-primary focus:ring-primary' }} rounded-sm focus:ring-1 transition-colors text-sm">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Đơn vị quản lý
                        </label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-slate-600 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa CNTT' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border {{ $errors->has('advisor_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-200' : 'border-slate-300 focus:border-primary focus:ring-primary' }} rounded-sm focus:ring-1 transition-colors text-sm cursor-pointer">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}"
                                    {{ old('advisor_id', $class->advisor_id) == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('advisor_id')
                            <p class="text-red-500 text-xs mt-1 italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="md:col-span-2 mt-2">
                        <div
                            class="p-4 bg-blue-50 border border-blue-100 rounded-sm {{ $errors->has('student_file') ? 'border-red-500 bg-red-50' : '' }}">
                            <label class="block text-sm font-bold text-slate-700 mb-2 flex items-center gap-2">
                                <span class="material-symbols-outlined text-blue-600">upload_file</span>
                                Import Danh sách Sinh viên (Tùy chọn)
                            </label>

                            <input type="file" name="student_file" id="student_file_input"
                                class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-sm file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer" />

                            @error('student_file')
                                <div class="mt-2 text-red-600 text-sm font-bold flex items-start gap-1">
                                    <span class="material-symbols-outlined !text-[18px]">warning</span>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror

                            <p class="text-xs text-slate-500 mt-2 ml-1">
                                Hỗ trợ file .xlsx, .csv. <span class="font-bold">Lưu ý:</span> Nếu mã SV bị trùng, quá trình
                                cập nhật sẽ bị hủy.
                            </p>

                            <p id="upload-error" class="text-red-500 text-xs mt-2 hidden font-bold"></p>
                            <div id="preview-area"></div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100">
                    <a href="{{ route('admin.classes.index') }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 text-sm">Hủy
                        bỏ</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 shadow-sm flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined !text-[18px]">save</span> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('student_file_input');
            const previewArea = document.getElementById('preview-area');
            const errorArea = document.getElementById('upload-error');

            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    let file = e.target.files[0];
                    if (!file) {
                        previewArea.innerHTML = '';
                        return;
                    }

                    let formData = new FormData();
                    formData.append('file', file);

                    previewArea.innerHTML = `
                        <div class="mt-4 text-center text-slate-500 text-sm flex items-center justify-center gap-2 py-4">
                            <span class="animate-spin material-symbols-outlined text-blue-600">progress_activity</span> 
                            Đang đọc dữ liệu...
                        </div>`;
                    errorArea.classList.add('hidden');

                    fetch('{{ route('admin.classes.upload.preview') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                previewArea.innerHTML = '';
                                errorArea.innerText = data.error;
                                errorArea.classList.remove('hidden');
                            } else {
                                previewArea.innerHTML = data.html;

                                if (data.hasError) {
                                    errorArea.innerText =
                                        'Cảnh báo: File có chứa Mã sinh viên đã tồn tại (dòng màu đỏ). Vui lòng kiểm tra lại trước khi lưu!';
                                    errorArea.classList.remove('hidden');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            previewArea.innerHTML = '';
                            errorArea.innerText = 'Có lỗi xảy ra khi tải file. Vui lòng thử lại.';
                            errorArea.classList.remove('hidden');
                        });
                });
            }
        });
    </script>
@endsection
