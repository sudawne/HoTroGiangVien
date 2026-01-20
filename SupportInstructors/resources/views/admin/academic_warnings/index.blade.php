@extends('layouts.admin')

@section('title', 'Cảnh báo học tập')

@section('content')
    <div class="max-w-[1400px] mx-auto">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
            <div>
                <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
                    <ol class="flex items-center space-x-2">
                        <li><a class="hover:text-primary transition-colors" href="{{ route('admin.dashboard') }}">Trang
                                chủ</a></li>
                        <li><span class="material-symbols-outlined !text-[12px]">chevron_right</span></li>
                        <li><span class="font-medium text-slate-900 dark:text-slate-200">Cảnh báo học tập</span></li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Cảnh báo học tập</h1>
                <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">Quản lý và theo dõi tình trạng học tập của sinh
                    viên.</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-4 py-2 rounded-sm font-medium transition-all shadow-sm text-sm">
                    {{-- Icon 20px -> 16px --}}
                    <span class="material-symbols-outlined !text-[16px]">send</span>
                    Gửi thông báo hàng loạt
                </button>
            </div>
        </div>

        {{-- THỐNG KÊ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

            {{-- Card 1: Tổng số cảnh báo --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Tổng số cảnh
                        báo</p>
                    <span class="bg-primary/10 text-primary p-1 rounded-sm">
                        {{-- Icon 18px -> 15px --}}
                        <span class="material-symbols-outlined !text-[15px]">warning</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">128</h2>
                    <span class="text-xs text-emerald-600 font-medium mb-1 flex items-center">
                        <span class="material-symbols-outlined !text-[12px]">trending_up</span> +12%
                    </span>
                </div>
            </div>

            {{-- Card 2: Mức 1 (Vàng) --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh báo mức
                        1</p>
                    <span class="bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-500 p-1 rounded-sm">
                        <span class="material-symbols-outlined !text-[15px]">info</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">74</h2>
                    <span class="text-xs text-slate-400 font-medium mb-1">
                        Đang theo dõi
                    </span>
                </div>
            </div>

            {{-- Card 3: Mức 2 (Cam) --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Cảnh báo mức
                        2</p>
                    <span class="bg-orange-50 dark:bg-orange-500/10 text-orange-600 dark:text-orange-500 p-1 rounded-sm">
                        <span class="material-symbols-outlined !text-[15px]">assignment_late</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">42</h2>
                    <span class="text-xs text-orange-600 dark:text-orange-400 font-medium mb-1 flex items-center gap-1">
                        Cần gặp cố vấn
                    </span>
                </div>
            </div>

            {{-- Card 4: Nguy cơ thôi học (Đỏ đậm) --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-4 flex flex-col justify-between h-28 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex justify-between items-start">
                    <p class="text-slate-500 dark:text-slate-400 text-xs font-semibold uppercase tracking-wide">Nguy cơ thôi
                        học</p>
                    <span class="bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-500 p-1 rounded-sm">
                        <span class="material-symbols-outlined !text-[15px]">dangerous</span>
                    </span>
                </div>
                <div class="flex items-end gap-2">
                    <h2 class="text-3xl font-bold text-slate-800 dark:text-white">12</h2>
                    <span class="text-xs text-red-600 dark:text-red-400 font-bold mb-1 flex items-center gap-1">
                        <span class="material-symbols-outlined !text-[12px]">priority_high</span> Khẩn cấp
                    </span>
                </div>
            </div>
        </div>

        {{-- BỘ LỌC & CÔNG CỤ --}}
        <div class="bg-white dark:bg-[#1e1e2d] p-4 rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 mb-6">

            {{-- Hàng 1: Tìm kiếm & Các nút thao tác --}}
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">

                {{-- Tìm kiếm nhanh --}}
                <div class="relative flex-1 max-w-md">
                    <span
                        class="material-symbols-outlined absolute left-3 top-2.5 text-slate-400 !text-[16px]">search</span>
                    <input
                        class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-9 pr-3 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300"
                        placeholder="Tìm nhanh tên hoặc MSSV..." type="text" />
                </div>

                {{-- Nhóm nút thao tác --}}
                <div class="flex flex-wrap items-center gap-2">

                    {{-- Nút Bật/Tắt Bộ lọc chi tiết --}}
                    <button id="toggleFilterBtn"
                        class="bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-200 dark:hover:bg-slate-700 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[16px]">filter_list</span>
                        Bộ lọc
                        <span id="filterArrow"
                            class="material-symbols-outlined !text-[16px] transition-transform duration-200">expand_more</span>
                    </button>

                    {{-- Nút Nhập Excel --}}
                    <button
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                        title="Nhập dữ liệu từ Excel">
                        <span class="material-symbols-outlined !text-[16px] text-blue-600">upload_file</span>
                        <span class="hidden sm:inline">Nhập Excel</span>
                    </button>

                    {{-- Nút Xuất Excel --}}
                    <button
                        class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium py-2 px-3 rounded-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center gap-2"
                        title="Xuất dữ liệu ra Excel">
                        <span class="material-symbols-outlined !text-[16px] text-green-600">table_view</span>
                        <span class="hidden sm:inline">Xuất Excel</span>
                    </button>

                    {{-- Nút Thêm Mới --}}
                    <button
                        class="bg-primary hover:bg-indigo-700 text-white text-sm font-medium py-2 px-3 rounded-sm transition-colors flex items-center gap-2 shadow-sm">
                        <span class="material-symbols-outlined !text-[16px]">add</span>
                        <span class="hidden sm:inline">Thêm mới</span>
                    </button>
                </div>
            </div>

            {{-- Hàng 2: Khu vực Lọc nâng cao (Mặc định ẩn) --}}
            <div id="filterPanel"
                class="hidden mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 transition-all duration-300 ease-in-out">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                    {{-- Select Học kỳ --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Học kỳ</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 appearance-none cursor-pointer">
                            <option>Học kỳ 1 - 2023-2024</option>
                            <option>Học kỳ 2 - 2023-2024</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 top-6 flex items-center px-2 pointer-events-none">
                            <span class="material-symbols-outlined text-slate-500 !text-[16px]">expand_more</span>
                        </div>
                    </div>

                    {{-- Select Mức độ --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Mức cảnh báo</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 appearance-none cursor-pointer">
                            <option value="">Tất cả mức độ</option>
                            <option>Mức 1</option>
                            <option>Mức 2</option>
                            <option>Nguy cơ thôi học</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 top-6 flex items-center px-2 pointer-events-none">
                            <span class="material-symbols-outlined text-slate-500 !text-[16px]">expand_more</span>
                        </div>
                    </div>

                    {{-- Select Lớp --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-slate-500 mb-1">Lớp sinh hoạt</label>
                        <select
                            class="w-full bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 rounded-sm text-sm py-2 pl-3 pr-8 focus:ring-primary focus:border-primary text-slate-700 dark:text-slate-300 appearance-none cursor-pointer">
                            <option value="">Tất cả lớp</option>
                            <option>K64-CNTT1</option>
                            <option>K64-CNTT2</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 top-6 flex items-center px-2 pointer-events-none">
                            <span class="material-symbols-outlined text-slate-500 !text-[16px]">expand_more</span>
                        </div>
                    </div>

                    {{-- Nút Áp dụng --}}
                    <div class="flex items-end">
                        <button
                            class="w-full bg-slate-900 dark:bg-primary text-white text-sm font-medium py-2 px-4 rounded-sm hover:bg-slate-800 dark:hover:bg-indigo-700 transition-colors h-[38px]">
                            Áp dụng bộ lọc
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- BẢNG DỮ LIỆU --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] rounded-sm shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-700">
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                MSSV</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Họ và tên</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Lớp</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                GPA</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-center">
                                Nợ tín</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Mức cảnh báo</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                Trạng thái</th>
                            <th
                                class="px-6 py-4 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">
                                Tác vụ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        {{-- Dòng 1: Cảnh báo Mức 2 (Dữ liệu cũ) --}}
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-primary font-mono">20110001</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-sm bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                                        NA</div>
                                    <div class="text-sm font-medium dark:text-slate-200">Nguyễn Văn An</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">K64-CNTT1</td>
                            <td class="px-6 py-4 text-sm text-center font-medium text-red-500">1.45</td>
                            <td class="px-6 py-4 text-sm text-center text-slate-600 dark:text-slate-400">12</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">Mức
                                    2</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-xs font-medium text-amber-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                    Chưa phản hồi
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Gửi tin nhắn">
                                        <span class="material-symbols-outlined !text-[15px]">chat_bubble</span>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Đặt lịch hẹn">
                                        <span class="material-symbols-outlined !text-[15px]">event</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Dòng 2: Sinh viên Giỏi (Trạng thái tốt) --}}
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-primary font-mono">20110002</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-sm bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        TB</div>
                                    <div class="text-sm font-medium dark:text-slate-200">Trần Thị Bích</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">K64-CNTT2</td>
                            <td class="px-6 py-4 text-sm text-center font-medium text-emerald-500">3.65</td>
                            <td class="px-6 py-4 text-sm text-center text-slate-600 dark:text-slate-400">0</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">An
                                    toàn</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Tốt
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Gửi tin nhắn">
                                        <span class="material-symbols-outlined !text-[15px]">chat_bubble</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Dòng 3: Cảnh báo Mức 1 (Đã tư vấn) --}}
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-primary font-mono">20110003</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-sm bg-amber-100 dark:bg-amber-900/50 flex items-center justify-center text-xs font-bold text-amber-600 dark:text-amber-400">
                                        LH</div>
                                    <div class="text-sm font-medium dark:text-slate-200">Lê Hoàng</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">K64-ATTT</td>
                            <td class="px-6 py-4 text-sm text-center font-medium text-amber-500">1.85</td>
                            <td class="px-6 py-4 text-sm text-center text-slate-600 dark:text-slate-400">4</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">Mức
                                    1</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="flex items-center gap-1.5 text-xs font-medium text-blue-600 dark:text-blue-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                    Đã tư vấn
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Gửi tin nhắn">
                                        <span class="material-symbols-outlined !text-[15px]">chat_bubble</span>
                                    </button>
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Đặt lịch hẹn">
                                        <span class="material-symbols-outlined !text-[15px]">event</span>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Dòng 4: Nguy cơ buộc thôi học (Cần chú ý) --}}
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
                            <td class="px-6 py-4 text-sm font-medium text-primary font-mono">20110004</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 rounded-sm bg-red-100 dark:bg-red-900/50 flex items-center justify-center text-xs font-bold text-red-600 dark:text-red-400">
                                        PQ</div>
                                    <div class="text-sm font-medium dark:text-slate-200">Phạm Quốc Huy</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">K63-DTVT</td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-red-600">0.85</td>
                            <td class="px-6 py-4 text-sm text-center font-bold text-red-500">24</td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2.5 py-0.5 rounded-sm text-xs font-bold bg-red-200 text-red-800 dark:bg-red-900/50 dark:text-red-300">Buộc
                                    thôi học?</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-xs font-bold text-red-600 dark:text-red-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 animate-pulse"></span>
                                    Khẩn cấp
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button class="p-1.5 text-slate-400 hover:text-primary transition-colors"
                                        title="Xem chi tiết">
                                        <span class="material-symbols-outlined !text-[15px]">visibility</span>
                                    </button>
                                    <button class="p-1.5 text-red-500 hover:bg-red-50 rounded transition-colors"
                                        title="Cảnh báo ngay">
                                        <span class="material-symbols-outlined !text-[15px]">notification_important</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Phân trang --}}
            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                <p class="text-sm text-slate-500 dark:text-slate-400">Hiển thị <span
                        class="font-medium text-slate-900 dark:text-slate-200">1 - 4</span> trong <span
                        class="font-medium text-slate-900 dark:text-slate-200">128</span> sinh viên</p>

                {{-- Pagination Mock --}}
                <div class="flex gap-1">
                    <button class="p-1 rounded hover:bg-slate-200 text-slate-500 disabled:opacity-50">
                        <span class="material-symbols-outlined !text-[14px]">chevron_left</span>
                    </button>
                    <button class="px-2.5 py-1 text-xs rounded bg-primary text-white font-medium">1</button>
                    <button class="px-2.5 py-1 text-xs rounded hover:bg-slate-200 text-slate-600 font-medium">2</button>
                    <button class="p-1 rounded hover:bg-slate-200 text-slate-500">
                        <span class="material-symbols-outlined !text-[14px]">chevron_right</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT XỬ LÝ ĐÓNG MỞ BỘ LỌC --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggleBtn = document.getElementById('toggleFilterBtn');
            const filterPanel = document.getElementById('filterPanel');
            const arrow = document.getElementById('filterArrow');

            toggleBtn.addEventListener('click', function() {
                // Toggle ẩn hiện
                filterPanel.classList.toggle('hidden');

                // Xoay mũi tên & đổi màu nút
                if (filterPanel.classList.contains('hidden')) {
                    arrow.style.transform = 'rotate(0deg)';
                    toggleBtn.classList.remove('bg-slate-200', 'dark:bg-slate-700');
                } else {
                    arrow.style.transform = 'rotate(180deg)';
                    toggleBtn.classList.add('bg-slate-200', 'dark:bg-slate-700');
                }
            });
        });
    </script>
@endsection
