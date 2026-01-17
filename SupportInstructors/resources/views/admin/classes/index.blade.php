@extends('layouts.admin')

@section('title', 'Quản lý Lớp 20DTHA2')

@section('content')
    <div class="max-w-[1600px] mx-auto flex flex-col gap-6">

        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-6 shadow-sm relative overflow-hidden group">
            <div
                class="absolute -top-6 -right-6 p-4 opacity-5 rotate-12 transition-transform group-hover:rotate-0 group-hover:opacity-10">
                <span class="material-symbols-outlined !text-[200px] text-primary">groups</span>
            </div>

            <div class="relative z-10 flex flex-col lg:flex-row justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-white">Lớp: 20DTHA2</h1>
                        <span
                            class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span> Đang hoạt động
                        </span>
                    </div>
                    <div class="flex flex-wrap gap-4 text-slate-500 dark:text-slate-400 text-sm font-medium">
                        <span class="flex items-center gap-1"><span
                                class="material-symbols-outlined text-primary !text-[18px]">domain</span> Ngành: CN Thông
                            tin</span>
                        <span class="flex items-center gap-1"><span
                                class="material-symbols-outlined text-primary !text-[18px]">school</span> Khóa: 2020 -
                            2024</span>
                        <span class="flex items-center gap-1"><span
                                class="material-symbols-outlined text-primary !text-[18px]">person_apron</span> GVCN: TS.
                            Nguyễn Văn A</span>
                    </div>
                </div>

                <div class="flex gap-4">
                    <div
                        class="px-5 py-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Tổng số</span>
                        <span class="text-2xl font-bold text-primary">65</span>
                    </div>
                    <div
                        class="px-5 py-3 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700 flex flex-col items-center min-w-[100px]">
                        <span class="text-xs text-slate-500 uppercase font-bold tracking-wider">Nam / Nữ</span>
                        <span class="text-2xl font-bold text-slate-700 dark:text-slate-300">40<span
                                class="text-sm text-slate-400 mx-1">/</span>25</span>
                    </div>
                    <div
                        class="px-5 py-3 bg-red-50 dark:bg-red-900/10 rounded-lg border border-red-100 dark:border-red-900/30 flex flex-col items-center min-w-[100px]">
                        <span class="text-xs text-red-500 uppercase font-bold tracking-wider">Cảnh báo</span>
                        <span class="text-2xl font-bold text-red-600">03</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <h3 class="font-bold text-slate-800 dark:text-white text-lg flex items-center gap-2">
                <span class="material-symbols-outlined text-primary">badge</span> Ban Cán Sự Lớp
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-primary/40 rounded-lg p-4 shadow-sm flex items-center gap-4 relative overflow-hidden">
                    <div class="absolute top-0 right-0 px-2 py-1 bg-primary text-white text-[10px] font-bold rounded-bl-lg">
                        LỚP TRƯỞNG</div>
                    <img src="https://ui-avatars.com/api/?name=Nguyen+Hung&background=random"
                        class="size-14 rounded-full border-2 border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">Nguyễn Văn Hùng</h4>
                        <p class="text-xs text-slate-500">MSSV: 20114521</p>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><span
                                class="material-symbols-outlined !text-[12px]">call</span> 0988 777 666</p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg p-4 shadow-sm flex items-center gap-4">
                    <div
                        class="absolute top-0 right-0 px-2 py-1 bg-slate-200 text-slate-600 text-[10px] font-bold rounded-bl-lg">
                        LỚP PHÓ HT</div>
                    <img src="https://ui-avatars.com/api/?name=Tran+Mai&background=random"
                        class="size-14 rounded-full border-2 border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">Trần Thị Mai</h4>
                        <p class="text-xs text-slate-500">MSSV: 20113321</p>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><span
                                class="material-symbols-outlined !text-[12px]">call</span> 0912 345 678</p>
                    </div>
                </div>
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg p-4 shadow-sm flex items-center gap-4">
                    <div
                        class="absolute top-0 right-0 px-2 py-1 bg-red-100 text-red-600 text-[10px] font-bold rounded-bl-lg">
                        BÍ THƯ</div>
                    <img src="https://ui-avatars.com/api/?name=Le+Cuong&background=random"
                        class="size-14 rounded-full border-2 border-slate-100">
                    <div>
                        <h4 class="font-bold text-slate-800 dark:text-white text-sm">Lê Văn Cường</h4>
                        <p class="text-xs text-slate-500">MSSV: 20119988</p>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1"><span
                                class="material-symbols-outlined !text-[12px]">call</span> 0909 111 222</p>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm flex flex-col">
            <div
                class="p-4 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <div class="relative w-full sm:w-64">
                        <span
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400"><span
                                class="material-symbols-outlined !text-[18px]">search</span></span>
                        <input
                            class="block w-full pl-9 pr-3 py-1.5 border border-slate-200 rounded text-sm focus:ring-primary focus:border-primary"
                            placeholder="Tìm MSSV, Tên...">
                    </div>
                    <button
                        class="px-3 py-1.5 bg-white border border-slate-200 rounded text-slate-600 hover:bg-slate-50 text-sm font-medium flex items-center gap-1">
                        <span class="material-symbols-outlined !text-[18px]">filter_list</span> Lọc
                    </button>
                </div>
                <div class="flex gap-2">
                    <button
                        class="px-3 py-1.5 bg-green-600 text-white rounded text-sm font-bold hover:bg-green-700 flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">upload_file</span> Nhập Excel
                    </button>
                    <button
                        class="px-3 py-1.5 bg-primary text-white rounded text-sm font-bold hover:bg-primary/90 flex items-center gap-1 shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">person_add</span> Thêm SV
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 text-xs uppercase border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 font-bold">Thông tin Sinh viên</th>
                            <th class="px-4 py-3 font-bold">Liên lạc</th>
                            <th class="px-4 py-3 font-bold text-center">GPA Tích lũy</th>
                            <th class="px-4 py-3 font-bold text-center">ĐRL</th>
                            <th class="px-4 py-3 font-bold text-center">Tình trạng</th>
                            <th class="px-4 py-3 font-bold text-right">Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Nguyen+Van+A&background=random"
                                        class="size-9 rounded-full">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white">Nguyễn Văn A</p>
                                        <p class="text-xs text-slate-500 font-mono">20110452</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 text-xs">
                                <p class="flex items-center gap-1 mb-0.5"><span
                                        class="material-symbols-outlined !text-[14px] text-slate-400">call</span> 0909 123
                                    456</p>
                                <p class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[14px] text-slate-400">mail</span>
                                    a.nv@st.edu.vn</p>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-slate-700 dark:text-white">3.24</td>
                            <td class="px-4 py-3 text-center"><span
                                    class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-bold">85</span></td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Đang
                                    học</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="#"
                                    class="text-primary hover:text-primary/80 text-xs font-bold hover:underline">Chi
                                    tiết</a>
                            </td>
                        </tr>
                        <tr
                            class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors bg-red-50/40 dark:bg-red-900/10">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=Tran+Thi+B&background=random"
                                        class="size-9 rounded-full">
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white">Trần Thị B</p>
                                        <p class="text-xs text-slate-500 font-mono">20110333</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-600 dark:text-slate-300 text-xs">
                                <p class="flex items-center gap-1 mb-0.5"><span
                                        class="material-symbols-outlined !text-[14px] text-slate-400">call</span> 0912 999
                                    888</p>
                                <p class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[14px] text-slate-400">mail</span>
                                    b.tt@st.edu.vn</p>
                            </td>
                            <td class="px-4 py-3 text-center font-bold text-red-600">1.85</td>
                            <td class="px-4 py-3 text-center"><span
                                    class="px-2 py-0.5 rounded bg-orange-100 text-orange-700 text-xs font-bold">60</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded text-[10px] font-bold bg-red-50 text-red-700 border border-red-200">Cảnh
                                    cáo</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="#"
                                    class="text-primary hover:text-primary/80 text-xs font-bold hover:underline">Chi
                                    tiết</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 flex justify-end">
            </div>
        </div>
    </div>
@endsection
