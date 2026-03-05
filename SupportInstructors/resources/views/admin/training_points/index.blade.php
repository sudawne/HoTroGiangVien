@extends('layouts.admin')

@section('title', 'Quản lý Điểm rèn luyện')

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col gap-6">

        {{-- HEADER & ACTIONS --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Điểm rèn luyện</h1>
                <p class="text-slate-500 text-sm mt-1">Quản lý, đánh giá và xếp loại điểm rèn luyện sinh viên theo học kỳ.</p>
            </div>
            <div class="flex gap-2">
                {{-- Nút Nhập Excel --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-lg hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-600/20">
                    <span class="material-symbols-outlined !text-[20px]">upload_file</span> Import Excel
                </button>
                {{-- Nút Xuất Excel --}}
                <button class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20">
                    <span class="material-symbols-outlined !text-[20px]">download</span> Xuất Báo cáo
                </button>
            </div>
        </div>

        {{-- STATS CARDS (Thống kê nhanh) --}}
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-bold uppercase">Xuất sắc (90-100)</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">12</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-bold uppercase">Tốt (80-89)</p>
                <p class="text-2xl font-bold text-blue-600 mt-1">45</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-bold uppercase">Khá (65-79)</p>
                <p class="text-2xl font-bold text-sky-500 mt-1">20</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-bold uppercase">Trung bình (50-64)</p>
                <p class="text-2xl font-bold text-orange-500 mt-1">5</p>
            </div>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs text-slate-500 font-bold uppercase">Yếu/Kém (<50)</p>
                <p class="text-2xl font-bold text-red-500 mt-1">1</p>
            </div>
        </div>

        {{-- MAIN LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- SIDEBAR FILTERS --}}
            <div class="lg:col-span-1 flex flex-col gap-4">
                <div class="bg-white dark:bg-[#1e1e2d] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm sticky top-24">
                    <h3 class="font-bold text-sm text-slate-800 uppercase mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">filter_alt</span> Bộ lọc
                    </h3>
                    
                    <form action="" method="GET" class="space-y-4">
                        {{-- Chọn Học kỳ --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Học kỳ / Năm học</label>
                            <select name="semester_id" class="w-full text-sm border-slate-300 rounded-lg focus:ring-primary focus:border-primary">
                                <option value="">Học kỳ 1 - 2025-2026</option>
                                <option value="">Học kỳ 2 - 2024-2025</option>
                            </select>
                        </div>

                        {{-- Chọn Lớp --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Lớp sinh hoạt</label>
                            <select name="class_id" class="w-full text-sm border-slate-300 rounded-lg focus:ring-primary focus:border-primary">
                                <option value="">Tất cả các lớp</option>
                                <option value="">ĐH CNTT K14</option>
                                <option value="">ĐH KTBC K15</option>
                            </select>
                        </div>

                        {{-- Tìm kiếm sinh viên --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1">Tìm kiếm</label>
                            <div class="relative">
                                <input type="text" name="search" placeholder="Tên hoặc MSSV..." class="w-full text-sm border-slate-300 rounded-lg pl-9 focus:ring-primary focus:border-primary">
                                <span class="material-symbols-outlined absolute left-2.5 top-2 text-slate-400 text-[18px]">search</span>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-2 bg-primary text-white font-bold rounded-lg hover:bg-primary/90 transition shadow-md">
                            Áp dụng lọc
                        </button>
                    </form>
                </div>
            </div>

            {{-- MAIN TABLE --}}
            <div class="lg:col-span-3">
                <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 text-xs uppercase text-slate-500 font-bold">
                                    <th class="p-4 text-center w-12">#</th>
                                    <th class="p-4">Sinh viên</th>
                                    <th class="p-4 text-center">SV Tự ĐG</th>
                                    <th class="p-4 text-center">Lớp ĐG</th>
                                    <th class="p-4 text-center">Khoa Duyệt</th>
                                    <th class="p-4 text-center">Xếp loại</th>
                                    <th class="p-4 text-right">Thao tác</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                                {{-- ITEM 1: XUẤT SẮC --}}
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 text-center font-medium text-slate-400">1</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800">Nguyễn Văn A</div>
                                        <div class="text-xs text-slate-500">MSSV: 21001234</div>
                                        <div class="text-[10px] text-primary bg-primary/10 px-1.5 py-0.5 rounded inline-block mt-1">ĐH CNTT K14</div>
                                    </td>
                                    <td class="p-4 text-center text-slate-500">95</td>
                                    <td class="p-4 text-center text-slate-500">92</td>
                                    <td class="p-4 text-center">
                                        <span class="font-bold text-emerald-600 text-base">92</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-emerald-100 text-emerald-700">
                                            Xuất sắc
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button class="text-slate-400 hover:text-primary transition-colors p-1" title="Chỉnh sửa điểm">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                    </td>
                                </tr>

                                {{-- ITEM 2: KHÁ --}}
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="p-4 text-center font-medium text-slate-400">2</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800">Trần Thị B</div>
                                        <div class="text-xs text-slate-500">MSSV: 21005678</div>
                                        <div class="text-[10px] text-primary bg-primary/10 px-1.5 py-0.5 rounded inline-block mt-1">ĐH CNTT K14</div>
                                    </td>
                                    <td class="p-4 text-center text-slate-500">80</td>
                                    <td class="p-4 text-center text-slate-500">75</td>
                                    <td class="p-4 text-center">
                                        <span class="font-bold text-sky-600 text-base">75</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-sky-100 text-sky-700">
                                            Khá
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button class="text-slate-400 hover:text-primary transition-colors p-1" title="Chỉnh sửa điểm">
                                            <span class="material-symbols-outlined text-[20px]">edit</span>
                                        </button>
                                    </td>
                                </tr>

                                {{-- ITEM 3: CHƯA CHẤM --}}
                                <tr class="hover:bg-slate-50 transition-colors bg-red-50/30">
                                    <td class="p-4 text-center font-medium text-slate-400">3</td>
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800">Lê Văn C</div>
                                        <div class="text-xs text-slate-500">MSSV: 21009999</div>
                                        <div class="text-[10px] text-primary bg-primary/10 px-1.5 py-0.5 rounded inline-block mt-1">ĐH KTBC K15</div>
                                    </td>
                                    <td class="p-4 text-center text-slate-400 italic">--</td>
                                    <td class="p-4 text-center text-slate-400 italic">--</td>
                                    <td class="p-4 text-center">
                                        <span class="font-bold text-slate-400">0</span>
                                    </td>
                                    <td class="p-4 text-center">
                                        <span class="px-2 py-1 rounded text-[10px] font-bold uppercase bg-slate-100 text-slate-500">
                                            Chưa xét
                                        </span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button class="text-slate-400 hover:text-primary transition-colors p-1" title="Nhập điểm">
                                            <span class="material-symbols-outlined text-[20px]">add_circle</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- PAGINATION --}}
                    <div class="p-4 border-t border-slate-200">
                        {{-- {{ $trainingPoints->links() }} --}}
                        <div class="flex justify-center text-xs text-slate-500">
                            Hiển thị 3 / 83 sinh viên
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection