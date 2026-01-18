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
                    <span class="material-symbols-outlined text-primary !text-[20px]">edit_square</span>
                    Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route('admin.classes.update', $class->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="code" value="{{ old('code', $class->code) }}" required
                            class="w-full pl-3 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors font-mono uppercase text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" value="{{ old('academic_year', $class->academic_year) }}"
                            required
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm">
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Đơn vị quản lý (Khoa)
                        </label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm text-slate-600 dark:text-slate-400 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa Công Nghệ Thông Tin & Truyền Thông' }}
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập (GV) <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm cursor-pointer bg-white dark:bg-slate-800">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}" {{ $class->advisor_id == $lec->id ? 'selected' : '' }}>
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('admin.classes.index') }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 transition-colors text-sm">
                        Hủy bỏ
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined !text-[18px]">save</span>
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
