@extends('layouts.admin')
@section('title', 'Quản lý Lớp học - Khoa CNTT')

@section('content')
    <div class="max-w-[1400px] mx-auto">

        <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Danh sách Lớp học</h1>
                <p class="text-sm text-slate-500 mt-1">Khoa Thông tin & Truyền thông</p>
            </div>
            <a href="{{ route('admin.classes.create') }}"
                class="bg-primary text-white px-4 py-2 rounded-lg text-sm font-bold shadow-sm hover:bg-primary/90 flex items-center gap-2">
                <span class="material-symbols-outlined !text-[18px]">add</span> Tạo lớp mới
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($classes as $class)
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow group relative overflow-visible">
                    <div
                        class="absolute top-0 left-0 w-1 h-full bg-primary group-hover:bg-indigo-400 transition-colors rounded-l-xl">
                    </div>

                    {{-- Menu Tác vụ --}}
                    <div class="absolute top-3 right-3 z-10" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="p-1.5 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-600 transition-colors">
                            <span class="material-symbols-outlined !text-[20px]">more_vert</span>
                        </button>

                        <div x-show="open" @click.away="open = false" style="display: none;"
                            class="absolute right-0 mt-1 w-36 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg py-1 z-20">
                            <a href="{{ route('admin.classes.edit', $class->id) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700">
                                <span class="material-symbols-outlined !text-[16px] text-blue-500">edit</span> Sửa
                            </a>
                            <form action="{{ route('admin.classes.destroy', $class->id) }}" method="POST"
                                onsubmit="return confirm('Bạn có chắc chắn muốn xóa lớp này?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 text-left">
                                    <span class="material-symbols-outlined !text-[16px]">delete</span> Xóa
                                </button>
                            </form>
                            <a href="{{ route('admin.classes.import', $class->id) }}"
                                class="flex items-center gap-2 px-4 py-2 text-sm text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 border-t border-slate-100">
                                <span class="material-symbols-outlined !text-[16px] text-green-600">upload_file</span>
                                Import SV
                            </a>
                        </div>
                    </div>

                    <div class="flex justify-between items-start mb-4 pl-3 pr-8">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $class->code }}</h3>
                            <p class="text-xs text-slate-500 font-medium">{{ Str::upper($class->name) }}</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="block text-2xl font-bold text-slate-700 dark:text-slate-200">{{ $class->students_count }}</span>
                            <span class="text-[10px] text-slate-400 uppercase">Sinh viên</span>
                        </div>
                    </div>

                    <div class="space-y-3 pl-3 border-t border-slate-100 dark:border-slate-700 pt-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                <span class="material-symbols-outlined !text-[18px]">school</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Cố vấn học tập</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">
                                    {{ $class->advisor->user->name ?? 'Chưa phân công' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                                <span class="material-symbols-outlined !text-[16px]">calendar_month</span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Niên khóa</p>
                                <p class="text-sm font-semibold text-slate-800 dark:text-white">{{ $class->academic_year }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="mt-4 pt-3 border-t border-dashed border-slate-200 dark:border-slate-700 pl-3 flex justify-between items-center text-xs">
                        <a href="{{ route('admin.classes.import', $class->id) }}"
                            class="text-slate-500 hover:text-green-600 font-medium flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[16px]">upload</span> Import
                        </a>
                        <a href="{{ route('admin.classes.show', $class->id) }}"
                            class="text-primary hover:underline font-medium flex items-center gap-1">
                            Xem danh sách SV <span class="material-symbols-outlined !text-[14px]">arrow_forward</span>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-10">
                    <p class="text-slate-500">Chưa có lớp học nào được tạo.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $classes->links() }}
        </div>
    </div>
@endsection
