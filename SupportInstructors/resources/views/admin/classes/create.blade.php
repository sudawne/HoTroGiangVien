@extends('layouts.admin')
@section('title', 'Thêm Lớp học mới')

@section('content')
    <div class="w-full px-4 py-6">

        {{-- Header Section --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[20px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Thêm Lớp Mới</h1>
                    <p class="text-xs text-slate-500">Nhập thông tin lớp học vào hệ thống</p>
                </div>
            </div>
        </div>

        {{-- Form Content --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">

            {{-- Form Title --}}
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[20px]">school</span>
                    Thông tin lớp học
                </h3>
            </div>

            <form action="{{ route('admin.classes.store') }}" method="POST" class="p-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Hàng 1: Mã lớp & Niên khóa --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Mã lớp <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" name="code" required placeholder="VD: 20DTHA1"
                                class="w-full pl-3 pr-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors font-mono uppercase text-sm">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">Mã định danh duy nhất (Unique)</p>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Niên khóa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="academic_year" required placeholder="VD: 2020-2024"
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm">
                    </div>

                    {{-- Hàng 2: Tên lớp (Full width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Tên lớp đầy đủ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" required placeholder="VD: Đại học Công nghệ thông tin K20A"
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm">
                    </div>

                    <div class="md:col-span-2 border-t border-slate-100 dark:border-slate-700 my-2"></div>

                    {{-- Hàng 3: Khoa & Cố vấn --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Đơn vị quản lý (Khoa)
                        </label>
                        <div
                            class="w-full px-3 py-2.5 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-sm text-slate-600 dark:text-slate-400 text-sm font-medium cursor-not-allowed">
                            {{ $department->name ?? 'Khoa Công Nghệ Thông Tin & Truyền Thông' }}
                        </div>
                        <input type="hidden" name="department_id" value="{{ $department->id ?? 1 }}">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 dark:text-slate-300 mb-1.5">
                            Cố vấn học tập (GV) <span class="text-red-500">*</span>
                        </label>
                        <select name="advisor_id" required
                            class="w-full px-3 py-2.5 border border-slate-300 dark:border-slate-600 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary transition-colors text-sm cursor-pointer bg-white dark:bg-slate-800">
                            <option value="">-- Chọn Giảng viên --</option>
                            @foreach ($lecturers as $lec)
                                <option value="{{ $lec->id }}">
                                    {{ $lec->lecturer_code }} - {{ $lec->user->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-400 mt-1">Giảng viên chịu trách nhiệm quản lý lớp.</p>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('admin.classes.index') }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 transition-colors text-sm">
                        Hủy bỏ
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined !text-[18px]">save</span>
                        Lưu Lớp Học
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
