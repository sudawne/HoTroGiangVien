@extends('layouts.admin')

@section('title', 'Biên bản Sinh hoạt lớp')

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col gap-6">

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Biên bản Sinh hoạt lớp</h1>
                <p class="text-slate-500 text-sm mt-1">Lưu trữ và quản lý các biên bản họp định kỳ, đột xuất và xét điểm rèn
                    luyện.</p>
            </div>
            <button
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined !text-[20px]">add</span> Tạo biên bản mới
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            <div class="lg:col-span-1 flex flex-col gap-4">
                <div
                    class="bg-white dark:bg-[#1e1e2d] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm sticky top-24">
                    <div class="mb-6">
                        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Năm học</h3>
                        <div class="space-y-1">
                            <label
                                class="flex items-center gap-3 p-2 rounded-lg bg-slate-50 dark:bg-slate-800 border border-primary/20 cursor-pointer">
                                <input type="radio" name="year" class="text-primary focus:ring-primary" checked>
                                <span class="text-sm font-bold text-primary">2023 - 2024</span>
                            </label>
                            <label
                                class="flex items-center gap-3 p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer border border-transparent">
                                <input type="radio" name="year" class="text-slate-400 focus:ring-slate-400">
                                <span class="text-sm font-medium text-slate-600 dark:text-slate-400">2022 - 2023</span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Loại biên bản</h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="px-3 py-1.5 bg-blue-50 text-blue-700 border border-blue-100 rounded-lg text-xs font-bold cursor-pointer transition-colors">Định
                                kỳ</span>
                            <span
                                class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-xs font-medium cursor-pointer hover:border-slate-300 hover:text-slate-700 transition-colors">Đột
                                xuất</span>
                            <span
                                class="px-3 py-1.5 bg-white border border-slate-200 text-slate-500 rounded-lg text-xs font-medium cursor-pointer hover:border-slate-300 hover:text-slate-700 transition-colors">Xét
                                ĐRL</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 flex flex-col gap-4">

                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded-bl-xl">
                        Đã duyệt</div>

                    <div class="flex gap-5">
                        <div
                            class="flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg w-20 h-20 flex-shrink-0 text-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Tháng</span>
                            <span class="text-3xl font-bold text-slate-800 dark:text-white leading-none">05</span>
                            <span class="text-[10px] font-bold text-slate-400">2024</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <h3
                                    class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors truncate">
                                    Biên bản họp lớp Tháng 05/2024</h3>
                            </div>

                            <div
                                class="flex flex-wrap gap-x-6 gap-y-2 mt-2 text-xs font-medium text-slate-500 uppercase tracking-wide">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">schedule</span> 09:00 - 10:30</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">location_on</span> Phòng C.102</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">group</span> Vắng: 03</span>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-3 line-clamp-2">
                                Nội dung chính: Phổ biến quy chế thi học kỳ 2, nhắc nhở đóng học phí, bầu chọn cán bộ lớp
                                nhiệm kỳ mới. Triển khai kế hoạch Mùa hè xanh 2024.
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="material-symbols-outlined !text-[16px]">edit_document</span> Người tạo: Nguyễn Văn
                            Hùng (Lớp trưởng)
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                                <span class="material-symbols-outlined !text-[16px]">visibility</span> Xem
                            </button>
                            <button
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-primary bg-primary/5 hover:bg-primary/10 transition-colors">
                                <span class="material-symbols-outlined !text-[16px]">download</span> Tải về
                            </button>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                    <div
                        class="absolute top-0 right-0 px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-bold uppercase rounded-bl-xl">
                        Chờ duyệt</div>

                    <div class="flex gap-5">
                        <div
                            class="flex flex-col items-center justify-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg w-20 h-20 flex-shrink-0 text-center">
                            <span class="text-xs font-bold text-slate-400 uppercase">Tháng</span>
                            <span class="text-3xl font-bold text-slate-800 dark:text-white leading-none">04</span>
                            <span class="text-[10px] font-bold text-slate-400">2024</span>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3
                                class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors truncate">
                                Biên bản xét Điểm rèn luyện HK1</h3>

                            <div
                                class="flex flex-wrap gap-x-6 gap-y-2 mt-2 text-xs font-medium text-slate-500 uppercase tracking-wide">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">schedule</span> 14:00 - 16:00</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">location_on</span> Online
                                    (Zoom)</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined !text-[16px]">group</span> Vắng: 00</span>
                            </div>

                            <p class="text-sm text-slate-600 dark:text-slate-400 mt-3 line-clamp-2">
                                Tổng kết điểm rèn luyện học kỳ 1 năm học 2023-2024. Giải quyết khiếu nại của sinh viên Trần
                                Thị B về điểm cộng tham gia hoạt động Đoàn.
                            </p>
                        </div>
                    </div>

                    <div
                        class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <span class="material-symbols-outlined !text-[16px]">edit_document</span> Người tạo: Lê Văn
                            Cường (Bí thư)
                        </div>
                        <div class="flex gap-2">
                            <button
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors">
                                <span class="material-symbols-outlined !text-[16px]">edit</span> Sửa đổi
                            </button>
                            <button
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-slate-600 hover:bg-slate-100 transition-colors">
                                <span class="material-symbols-outlined !text-[16px]">download</span> Tải về
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
