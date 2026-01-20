@extends('layouts.admin')
@section('title', 'Danh sách Sinh viên - ' . $class->code)

@section('content')
    <div class="w-full px-4 py-6">

        {{-- 1. HEADER: THÔNG TIN LỚP HỌC --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.classes.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 shadow-sm transition-colors">
                    {{-- Icon 20px -> 16px --}}
                    <span class="material-symbols-outlined !text-[16px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        {{ $class->name }}
                        <span
                            class="text-sm font-mono font-normal bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 px-2 py-0.5 rounded border border-slate-200 dark:border-slate-600">
                            {{ $class->code }}
                        </span>
                    </h1>
                    <div class="flex items-center gap-4 mt-1 text-sm text-slate-500">
                        <span class="flex items-center gap-1">
                            {{-- Icon 16px -> 14px --}}
                            <span class="material-symbols-outlined !text-[14px]">school</span>
                            GV: <span
                                class="font-semibold text-slate-700 dark:text-slate-300">{{ $class->advisor->user->name ?? 'Chưa phân công' }}</span>
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            {{-- Icon 16px -> 14px --}}
                            <span class="material-symbols-outlined !text-[14px]">calendar_month</span>
                            Niên khóa: {{ $class->academic_year }}
                        </span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span class="flex items-center gap-1">
                            {{-- Icon 16px -> 14px --}}
                            <span class="material-symbols-outlined !text-[14px]">groups</span>
                            Sĩ số: <span class="font-bold text-primary">{{ $class->students()->count() }}</span>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-2">
                {{-- Nút Sửa Lớp --}}
                <a href="{{ route('admin.classes.edit', $class->id) }}"
                    class="flex items-center gap-2 px-3 py-2 bg-white border border-slate-300 rounded-sm text-slate-700 hover:bg-slate-50 transition-colors text-sm font-medium">
                    {{-- Icon 18px -> 15px --}}
                    <span class="material-symbols-outlined !text-[15px]">settings</span> Cài đặt
                </a>
            </div>
        </div>

        {{-- 2. TOOLBAR & DANH SÁCH SINH VIÊN --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">

            {{-- Toolbar --}}
            <div
                class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h3 class="font-bold text-slate-800 dark:text-white text-base">Danh sách Sinh viên</h3>

                <div class="flex gap-2">
                    {{-- Nút Import Excel --}}
                    <a href="{{ route('admin.classes.import', $class->id) }}"
                        class="flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-sm hover:bg-green-700 transition-colors shadow-sm">
                        {{-- Icon 18px -> 15px --}}
                        <span class="material-symbols-outlined !text-[15px]">upload_file</span> Import Excel
                    </a>

                    {{-- Nút Thêm Mới --}}
                    <a href="{{ route('admin.students.create', ['class_id' => $class->id]) }}"
                        class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-sm hover:bg-primary/90 transition-colors shadow-sm">
                        {{-- Icon 18px -> 15px --}}
                        <span class="material-symbols-outlined !text-[15px]">person_add</span> Thêm SV
                    </a>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-slate-500 uppercase font-semibold text-xs">
                        <tr>
                            <th class="px-6 py-3 w-16 text-center">STT</th>
                            <th class="px-6 py-3 w-32">Mã SV</th>
                            <th class="px-6 py-3">Họ và Tên</th>
                            <th class="px-6 py-3 w-32">Ngày sinh</th>
                            <th class="px-6 py-3 w-32">Trạng thái</th>
                            <th class="px-6 py-3 w-24 text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($students as $index => $st)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-6 py-3 text-center text-slate-500">
                                    {{ $students->firstItem() + $index }}
                                </td>
                                <td class="px-6 py-3 font-mono font-bold text-primary">
                                    {{ $st->student_code }}
                                </td>
                                <td class="px-6 py-3">
                                    <div class="font-medium text-slate-800 dark:text-white">{{ $st->fullname }}</div>
                                    @if ($class->monitor_id == $st->id)
                                        <span
                                            class="text-[10px] bg-yellow-100 text-yellow-700 px-1.5 py-0.5 rounded border border-yellow-200 font-bold mt-1 inline-block">Lớp
                                            trưởng</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-slate-500">
                                    {{ $st->dob ? \Carbon\Carbon::parse($st->dob)->format('d/m/Y') : '--' }}
                                </td>
                                <td class="px-6 py-3">
                                    @if ($st->status == 'studying')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 border border-emerald-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Đang học
                                        </span>
                                    @elseif($st->status == 'dropped')
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 border border-red-200">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Thôi học
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                            {{ Str::upper($st->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div
                                        class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.students.show', $st->id) }}"
                                            class="p-1.5 hover:bg-blue-50 text-blue-600 rounded" title="Xem hồ sơ">
                                            {{-- Icon 18px -> 15px --}}
                                            <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                        </a>
                                        <a href="{{ route('admin.students.edit', $st->id) }}"
                                            class="p-1.5 hover:bg-orange-50 text-orange-600 rounded" title="Sửa">
                                            {{-- Icon 18px -> 15px --}}
                                            <span class="material-symbols-outlined !text-[15px]">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        {{-- Icon 48px -> 36px --}}
                                        <span
                                            class="material-symbols-outlined !text-[36px] mb-2 opacity-50">group_off</span>
                                        <p class="text-sm">Lớp này chưa có sinh viên nào.</p>
                                        <p class="text-xs mt-1">Hãy bấm nút "Import Excel" để thêm nhanh danh sách.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($students->hasPages())
                <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                    {{ $students->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
