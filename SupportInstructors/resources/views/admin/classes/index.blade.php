@extends('layouts.admin')
@section('title', 'Quản lý Lớp học')

@section('content')
    <div class="max-w-[1400px] mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-8">
            <div>
                <h1
                    class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-wide border-l-4 border-primary pl-3">
                    Danh sách Lớp học
                </h1>
                <p class="text-xs text-slate-500 mt-1 pl-4">Quản lý hồ sơ và sinh viên trực thuộc</p>
            </div>

            <div class="flex gap-2">

                <a href="{{ route('admin.classes.create') }}"
                    class="bg-slate-800 hover:bg-slate-700 dark:bg-white dark:hover:bg-slate-200 text-white dark:text-slate-900 px-4 py-1.5 rounded-sm text-xs font-bold uppercase tracking-wider shadow-sm transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[16px]">add</span> Tạo mới
                </a>
            </div>
        </div>

        {{-- GRID LAYOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
            @forelse($classes as $class)
                {{-- CARD: Technical Style --}}
                <div
                    class="group relative flex flex-col bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 border-l-4 border-l-primary/80 hover:border-l-primary rounded-r-sm shadow-sm hover:shadow-lg transition-all duration-300 h-full">

                    {{-- 1. Main Content --}}
                    <div class="p-5 flex-1">
                        <div class="flex justify-between items-start">
                            <div class="flex flex-col gap-1">
                                {{-- Technical ID --}}
                                <span class="font-mono text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                    Mã Lớp: {{ $class->code }}
                                </span>
                                {{-- Class Name --}}
                                <a href="{{ route('admin.classes.show', $class->id) }}"
                                    class="text-base font-bold text-slate-800 dark:text-white hover:text-primary transition-colors line-clamp-1"
                                    title="{{ $class->name }}">
                                    {{ Str::upper($class->name) }}
                                </a>
                            </div>

                            {{-- Menu Hover --}}
                            <div class="relative group/menu -mr-2 -mt-2">
                                <button
                                    class="p-2 text-slate-300 hover:text-slate-600 dark:text-slate-600 dark:hover:text-slate-300 transition-colors">
                                    <span class="material-symbols-outlined !text-[20px]">more_vert</span>
                                </button>
                                {{-- Dropdown --}}
                                <div class="hidden group-hover/menu:block absolute right-0 top-8 w-44 z-20">
                                    <div
                                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-600 rounded-sm shadow-xl py-1">
                                        <a href="{{ route('admin.classes.edit', $class->id) }}"
                                            class="flex items-center gap-3 px-4 py-2 text-xs font-medium text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                            <span class="material-symbols-outlined !text-[14px]">edit</span> Sửa thông tin
                                        </a>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST"
                                            onsubmit="return confirm('Xóa lớp này?');">
                                            @csrf @method('DELETE')
                                            <button
                                                class="w-full flex items-center gap-3 px-4 py-2 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                <span class="material-symbols-outlined !text-[14px]">delete</span> Xóa lớp
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Advisor Info --}}
                        <div class="mt-4 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-sm bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 border border-slate-200 dark:border-slate-600">
                                <span class="material-symbols-outlined !text-[16px]">person</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="text-[10px] text-slate-400 font-medium">Cố vấn học tập</span>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">
                                    {{ $class->advisor->user->name ?? 'Chưa cập nhật' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Technical Footer --}}
                    <div
                        class="bg-slate-50 dark:bg-slate-800/50 border-t border-slate-100 dark:border-slate-700 px-5 py-3 flex justify-between items-center text-xs">
                        <div class="flex gap-4">
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-400 uppercase font-bold">Niên khóa</span>
                                <span
                                    class="font-mono font-medium text-slate-600 dark:text-slate-300">{{ $class->academic_year }}</span>
                            </div>
                            <div class="w-[1px] h-6 bg-slate-200 dark:border-slate-600"></div>
                            <div class="flex flex-col">
                                <span class="text-[9px] text-slate-400 uppercase font-bold">Sĩ số</span>
                                <span
                                    class="font-mono font-medium text-slate-600 dark:text-slate-300">{{ $class->students_count }}</span>
                            </div>
                        </div>

                        <a href="{{ route('admin.classes.show', $class->id) }}"
                            class="group/btn flex items-center gap-1 text-slate-500 hover:text-primary transition-colors font-medium">
                            Chi tiết <span
                                class="material-symbols-outlined !text-[14px] group-hover/btn:translate-x-1 transition-transform">arrow_right_alt</span>
                        </a>
                    </div>
                </div>
            @empty
                {{-- Empty State Technical --}}
                <div
                    class="col-span-full py-20 flex flex-col items-center justify-center border border-dashed border-slate-300 dark:border-slate-700 rounded-sm bg-slate-50/30">
                    <span class="material-symbols-outlined !text-[40px] text-slate-300 mb-3">grid_off</span>
                    <h3 class="text-sm font-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Không có dữ
                        liệu</h3>
                    <p class="text-xs text-slate-400 mt-1 mb-5">Hệ thống chưa ghi nhận lớp học nào.</p>
                    <a href="{{ route('admin.classes.create') }}"
                        class="px-5 py-2 bg-slate-800 text-white text-xs font-bold uppercase tracking-wider hover:bg-slate-700 rounded-sm transition-colors">
                        Khởi tạo lớp mới
                    </a>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $classes->links() }}
        </div>
    </div>
@endsection
