@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col gap-6">

        {{-- CARDS GRID: Responsive Grid (1 cột mobile, 2 cột tablet, 4 cột desktop) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- Card 1 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Tổng số sinh
                        viên</p>
                    <span class="bg-primary/10 text-primary p-1 rounded">
                        {{-- Icon Card: 15px --}}
                        <span class="material-symbols-outlined !text-[15px]">groups</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">65</h2>
                    <span class="text-xs text-emerald-600 font-medium mb-1 flex items-center">
                        <span class="material-symbols-outlined !text-[12px]">trending_up</span> +2
                    </span>
                </div>
            </div>

            {{-- Card 2 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-red-100 dark:border-red-900/30 rounded p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute right-0 top-0 w-16 h-16 bg-red-50 dark:bg-red-900/10 rounded-bl-full -mr-4 -mt-4">
                </div>
                <div class="flex justify-between items-start relative z-10">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh cáo học
                        tập</p>
                    <span class="bg-red-100 text-red-600 p-1 rounded">
                        <span class="material-symbols-outlined !text-[15px]">warning</span>
                    </span>
                </div>
                <div class="flex items-end gap-2 relative z-10">
                    <h2 class="text-3xl font-bold text-red-600">3</h2>
                    <span class="text-xs text-slate-400 mb-1">sinh viên</span>
                </div>
            </div>

            {{-- Card 3 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Chưa đóng
                        học phí</p>
                    <span class="bg-orange-100 text-orange-600 p-1 rounded">
                        <span class="material-symbols-outlined !text-[15px]">payments</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">12</h2>
                    <span class="text-xs text-orange-600 font-medium mb-1">Cần nhắc nhở</span>
                </div>
            </div>

            {{-- Card 4 --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Biên bản họp
                        lớp</p>
                    <span class="bg-indigo-100 text-indigo-600 p-1 rounded">
                        <span class="material-symbols-outlined !text-[15px]">assignment</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">5</h2>
                    <span class="text-xs text-indigo-600 font-medium mb-1">Đã tạo kỳ này</span>
                </div>
            </div>
        </div>

        {{-- BỘ LỌC VÀ BUTTONS --}}
        <div
            class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-white dark:bg-[#1e1e2d] p-3 rounded border border-slate-200 dark:border-slate-700 shadow-sm">
            <div class="flex items-center gap-2 w-full md:w-auto">
                <span class="text-sm font-medium text-slate-600 dark:text-slate-300 ml-1 hidden sm:inline">Bộ lọc:</span>
                <div class="relative flex-1 md:flex-none">
                    <select
                        class="w-full md:w-auto appearance-none bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm rounded pl-3 pr-8 py-1.5 focus:outline-none focus:ring-1 focus:ring-primary cursor-pointer font-medium">
                        <option>Học kỳ 2 - 2023-2024</option>
                        <option>Học kỳ 1 - 2023-2024</option>
                        <option>Học kỳ 2 - 2022-2023</option>
                    </select>
                    <span class="absolute right-2 top-2 pointer-events-none text-slate-500">
                        <span class="material-symbols-outlined !text-[16px]">expand_more</span>
                    </span>
                </div>
                <div class="h-6 w-[1px] bg-slate-200 dark:bg-slate-700 mx-2 hidden md:block"></div>
                <button class="p-1.5 hover:bg-slate-100 dark:hover:bg-slate-700 rounded text-slate-500 hidden md:block"
                    title="Refresh">
                    <span class="material-symbols-outlined !text-[16px]">refresh</span>
                </button>
            </div>

            <div class="grid grid-cols-2 sm:flex gap-2 w-full sm:w-auto">
                <button
                    class="flex items-center justify-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium rounded hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined !text-[15px] text-green-600">table_view</span>
                    <span class="hidden sm:inline">Nhập Excel</span>
                    <span class="sm:hidden">Excel</span>
                </button>
                <button
                    class="flex items-center justify-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200 text-sm font-medium rounded hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                    <span class="material-symbols-outlined !text-[15px] text-red-500">picture_as_pdf</span>
                    <span class="hidden sm:inline">Xuất Báo cáo</span>
                    <span class="sm:hidden">Báo cáo</span>
                </button>
                <button
                    class="col-span-2 sm:col-span-1 flex items-center justify-center gap-2 px-3 py-1.5 bg-primary text-white text-sm font-bold rounded hover:bg-primary/90 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[15px]">add</span>
                    Thêm Sinh viên
                </button>
            </div>
        </div>

        {{-- DANH SÁCH SINH VIÊN & WIDGETS --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Bảng Sinh Viên --}}
            <div
                class="lg:col-span-2 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded flex flex-col shadow-sm overflow-hidden">
                <div
                    class="px-4 py-3 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-slate-50/50 dark:bg-slate-800/30">
                    <h3 class="font-bold text-slate-800 dark:text-white truncate">Danh sách Sinh viên cần theo dõi</h3>
                    <a class="text-xs font-medium text-primary hover:text-primary/80 whitespace-nowrap ml-2"
                        href="#">Xem tất cả</a>
                </div>

                {{-- Responsive Table Wrapper --}}
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse min-w-[700px]">
                        <thead>
                            <tr
                                class="text-xs text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50">
                                <th class="px-4 py-3 font-semibold w-24">MSSV</th>
                                <th class="px-4 py-3 font-semibold">Họ và Tên</th>
                                <th class="px-4 py-3 font-semibold">Lớp</th>
                                <th class="px-4 py-3 font-semibold w-16 text-center">GPA</th>
                                <th class="px-4 py-3 font-semibold w-32">ĐRL</th>
                                <th class="px-4 py-3 font-semibold">Trạng thái</th>
                                <th class="px-4 py-3 font-semibold text-right">Tác vụ</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-slate-100 dark:divide-slate-700">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-4 py-2.5 font-mono text-slate-600 dark:text-slate-300 text-xs">20110452</td>
                                <td class="px-4 py-2.5 font-medium text-slate-800 dark:text-slate-200">Nguyễn Văn A</td>
                                <td class="px-4 py-2.5 text-slate-500 text-xs">20DTHA2</td>
                                <td class="px-4 py-2.5 text-center font-bold text-slate-700 dark:text-slate-300">3.4</td>
                                <td class="px-4 py-2.5">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-500 rounded-full" style="width: 85%;"></div>
                                        </div>
                                        <span class="text-xs font-medium text-emerald-600">85</span>
                                    </div>
                                </td>
                                <td class="px-4 py-2.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">
                                        Đang học
                                    </span>
                                </td>
                                <td class="px-4 py-2.5 text-right">
                                    <button class="text-slate-400 hover:text-primary transition-colors">
                                        <span class="material-symbols-outlined !text-[15px]">edit_square</span>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div
                    class="px-4 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 flex items-center justify-between">
                    <span class="text-xs text-slate-500 hidden sm:inline">Hiển thị 5 trong số 65 sinh viên</span>
                    <div class="flex gap-1 ml-auto sm:ml-0">
                        <button class="p-1 rounded hover:bg-slate-200 text-slate-500 disabled:opacity-50">
                            <span class="material-symbols-outlined !text-[15px]">chevron_left</span>
                        </button>
                        <button class="px-2.5 py-1 text-xs rounded bg-primary text-white font-medium">1</button>
                        <button class="px-2.5 py-1 text-xs rounded hover:bg-slate-200 text-slate-600 font-medium">2</button>
                        <button class="px-2.5 py-1 text-xs rounded hover:bg-slate-200 text-slate-600 font-medium">3</button>
                        <button class="p-1 rounded hover:bg-slate-200 text-slate-500">
                            <span class="material-symbols-outlined !text-[15px]">chevron_right</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Cột phải: Thông báo & Lịch --}}
            <div class="flex flex-col gap-6">

                {{-- Thông báo Widget --}}
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col shadow-sm">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-orange-500 !text-[16px]">campaign</span>
                            Thông báo mới
                        </h3>
                        <a class="text-[10px] text-slate-400 hover:text-primary uppercase font-bold tracking-wider"
                            href="#">Xem hết</a>
                    </div>
                    <div class="flex flex-col gap-4">
                        <div
                            class="flex gap-3 items-start pb-3 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                            <div class="mt-1 min-w-1.5 h-1.5 rounded-full bg-primary"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-snug">Nhắc nhở
                                    đăng ký tín chỉ HK2</p>
                                <p class="text-xs text-slate-400 mt-1">12/05/2024 • Phòng Đào tạo</p>
                            </div>
                        </div>
                        <div
                            class="flex gap-3 items-start pb-3 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                            <div class="mt-1 min-w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-snug">Hạn nộp hồ
                                    sơ xét học bổng</p>
                                <p class="text-xs text-slate-400 mt-1">10/05/2024 • Phòng Công tác SV</p>
                            </div>
                        </div>
                        <div
                            class="flex gap-3 items-start pb-3 border-b border-slate-100 dark:border-slate-800 last:border-0 last:pb-0">
                            <div class="mt-1 min-w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                            <div>
                                <p class="text-sm font-medium text-slate-800 dark:text-slate-200 leading-snug">Kết quả đánh
                                    giá rèn luyện đợt 1</p>
                                <p class="text-xs text-slate-400 mt-1">08/05/2024 • Hệ thống</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Lịch Widget --}}
                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded p-4 flex flex-col shadow-sm flex-1">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary !text-[16px]">calendar_month</span>
                            Lịch gặp sinh viên
                        </h3>
                        <button class="p-1 hover:bg-slate-100 rounded text-slate-400">
                            <span class="material-symbols-outlined !text-[15px]">add</span>
                        </button>
                    </div>
                    <div class="flex flex-col gap-3">
                        <div
                            class="flex gap-3 bg-slate-50 dark:bg-slate-800/50 p-2 rounded border border-slate-100 dark:border-slate-800">
                            <div
                                class="flex flex-col items-center justify-center bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded px-2 py-1 min-w-[45px]">
                                <span class="text-[10px] font-bold text-red-500 uppercase">T.Hai</span>
                                <span class="text-lg font-bold text-slate-800 dark:text-white leading-none">20</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">Họp lớp định kỳ
                                    tháng 5</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span
                                        class="text-[10px] bg-slate-200 dark:bg-slate-600 px-1.5 py-0.5 rounded text-slate-600 dark:text-slate-300">09:00
                                        AM</span>
                                    <span class="text-xs text-slate-500 truncate">Phòng C.102</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
