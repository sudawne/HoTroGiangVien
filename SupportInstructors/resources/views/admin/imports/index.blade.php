@extends('layouts.admin')
@section('title', 'Trung tâm Dữ liệu (Import)')

@section('content')
    <div class="max-w-[1200px] mx-auto">
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Trung tâm Nhập liệu (Batch Import)</h1>
            <p class="text-sm text-slate-500 mt-1">Nạp dữ liệu từ Excel vào hệ thống (Điểm, Cảnh báo, Rèn luyện...). Dữ liệu
                sẽ ở trạng thái <strong>Pending</strong> trước khi được công bố.</p>
        </div>

        {{-- Import Area --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            {{-- Upload Box --}}
            <div
                class="md:col-span-2 bg-white dark:bg-[#1e1e2d] border-2 border-dashed border-primary/30 rounded-xl p-8 flex flex-col items-center justify-center text-center hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors cursor-pointer relative group">
                <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                <div class="bg-primary/10 p-4 rounded-full text-primary mb-4 group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined !text-[40px]">cloud_upload</span>
                </div>
                <h3 class="text-lg font-bold text-slate-700 dark:text-white">Kéo thả file Excel vào đây</h3>
                <p class="text-sm text-slate-500 mt-2">hoặc click để chọn file từ máy tính (.xlsx, .csv)</p>
                <p class="text-xs text-slate-400 mt-6">Hỗ trợ tối đa 10MB/file</p>
            </div>

            {{-- Configuration Box --}}
            <div
                class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-6 shadow-sm flex flex-col justify-center gap-4">
                <h3 class="font-bold text-slate-800 border-b pb-2">Cấu hình Import</h3>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Loại dữ liệu</label>
                    <select class="w-full rounded border-slate-300 focus:ring-primary text-sm">
                        <option value="warning">⚠️ Cảnh báo học vụ</option>
                        <option value="result">📊 Kết quả học tập (GPA)</option>
                        <option value="training_point">⭐ Điểm rèn luyện</option>
                        <option value="debt">💰 Danh sách nợ môn/HP</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Học kỳ áp dụng</label>
                    <select class="w-full rounded border-slate-300 focus:ring-primary text-sm">
                        <option>HK1 2025-2026 (Hiện tại)</option>
                        <option>HK2 2024-2025</option>
                    </select>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 uppercase mb-1 block">Tên lô (Batch Name)</label>
                    <input type="text" class="w-full rounded border-slate-300 text-sm"
                        placeholder="VD: Đợt 1 - Cảnh báo HK1">
                </div>

                <button class="w-full bg-primary text-white font-bold py-2 rounded hover:bg-primary/90 mt-2">
                    Tiến hành Xử lý
                </button>
            </div>
        </div>

        {{-- Recent Batches History --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 bg-slate-50/50">
                <h3 class="font-bold text-slate-800 dark:text-white">Lịch sử Import gần đây</h3>
            </div>
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-200">
                    <tr>
                        <th class="px-6 py-3">Tên Batch</th>
                        <th class="px-6 py-3">Loại</th>
                        <th class="px-6 py-3">Người tạo</th>
                        <th class="px-6 py-3">Số dòng</th>
                        <th class="px-6 py-3">Trạng thái</th>
                        <th class="px-6 py-3 text-right">Ngày tạo</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr class="hover:bg-slate-50 group">
                        <td class="px-6 py-3 font-medium text-slate-800">DS Cảnh báo HK1 25-26 (Đợt 1)</td>
                        <td class="px-6 py-3">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Warning</span>
                        </td>
                        <td class="px-6 py-3 text-slate-500">Admin</td>
                        <td class="px-6 py-3">120</td>
                        <td class="px-6 py-3">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-yellow-100 text-yellow-700 border border-yellow-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-yellow-600"></span> Pending
                            </span>
                            <button class="ml-2 text-xs text-primary hover:underline group-hover:inline-block hidden">Công
                                bố</button>
                        </td>
                        <td class="px-6 py-3 text-right text-slate-400">10 phút trước</td>
                    </tr>
                    <tr class="hover:bg-slate-50">
                        <td class="px-6 py-3 font-medium text-slate-800">Kết quả Rèn luyện HK2 24-25</td>
                        <td class="px-6 py-3">
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700">Training
                                Point</span>
                        </td>
                        <td class="px-6 py-3 text-slate-500">Admin</td>
                        <td class="px-6 py-3">1,500</td>
                        <td class="px-6 py-3">
                            <span
                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-bold bg-green-100 text-green-700 border border-green-200">
                                <span class="material-symbols-outlined !text-[12px]">check</span> Published
                            </span>
                        </td>
                        <td class="px-6 py-3 text-right text-slate-400">2 ngày trước</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
