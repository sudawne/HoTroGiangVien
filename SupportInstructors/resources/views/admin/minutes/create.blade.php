@extends('layouts.admin')
@section('title', 'Tạo biên bản họp lớp')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('admin.minutes.index') }}"
                class="p-2 rounded-full bg-white border hover:bg-slate-50 text-slate-500">
                <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
            </a>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Biên bản họp lớp mới</h1>
        </div>

        <form class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content Column --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- General Info Card --}}
                <div
                    class="bg-white dark:bg-[#1e1e2d] p-6 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-primary">info</span> Thông tin chung
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tiêu đề cuộc
                                họp <span class="text-red-500">*</span></label>
                            <input type="text"
                                class="w-full rounded border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-800 focus:ring-primary"
                                placeholder="VD: Sinh hoạt lớp tháng 10/2025">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Lớp <span
                                        class="text-red-500">*</span></label>
                                <select class="w-full rounded border-slate-300 bg-slate-50 focus:ring-primary">
                                    <option>20DTHA2</option>
                                    <option>21DTHA1</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Học kỳ</label>
                                <select class="w-full rounded border-slate-300 bg-slate-50 focus:ring-primary">
                                    <option>HK1 2025-2026</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Content Editor Card --}}
                <div
                    class="bg-white dark:bg-[#1e1e2d] p-6 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm">
                    <h3 class="font-bold text-slate-800 dark:text-white mb-4">Nội dung cuộc họp</h3>
                    {{-- Placeholder for WYSIWYG Editor (TinyMCE/CKEditor) --}}
                    <div class="border border-slate-300 rounded-lg min-h-[300px] bg-slate-50 p-4">
                        <p class="text-slate-400 italic text-center mt-20">[Khu vực tích hợp trình soạn thảo văn bản]</p>
                    </div>
                </div>
            </div>

            {{-- Sidebar Settings Column --}}
            <div class="space-y-6">

                {{-- Time & Place --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-5 rounded-lg border border-slate-200 shadow-sm">
                    <h3 class="font-semibold text-slate-800 mb-3">Thời gian & Địa điểm</h3>
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">Ngày giờ</label>
                            <input type="datetime-local" class="w-full mt-1 rounded border-slate-300 text-sm">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 uppercase">Phòng họp</label>
                            <input type="text" class="w-full mt-1 rounded border-slate-300 text-sm"
                                placeholder="VD: C.102">
                        </div>
                    </div>
                </div>

                {{-- Attendance --}}
                <div class="bg-white dark:bg-[#1e1e2d] p-5 rounded-lg border border-slate-200 shadow-sm"
                    x-data="{ attendees: 60 }">
                    <h3 class="font-semibold text-slate-800 mb-3">Điểm danh</h3>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-sm text-slate-600">Sĩ số lớp: <strong class="text-slate-900">65</strong></span>
                        <span class="text-sm text-slate-600">Vắng: <strong class="text-red-500"
                                x-text="65 - attendees"></strong></span>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase block mb-1">Số lượng tham gia</label>
                        <input type="number" x-model="attendees" max="65" min="0"
                            class="w-full rounded border-slate-300 text-sm">
                    </div>
                    <div class="mt-4">
                        <label class="text-xs font-bold text-slate-500 uppercase block mb-1">DSSV Vắng (Chọn nhiều)</label>
                        <select multiple class="w-full rounded border-slate-300 text-sm h-32 bg-slate-50">
                            <option value="1">Nguyễn Văn A (201101)</option>
                            <option value="2">Trần Thị B (201102)</option>
                        </select>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col gap-2">
                    <button type="button"
                        class="w-full py-2.5 bg-slate-200 text-slate-700 font-bold rounded hover:bg-slate-300 transition-colors">Lưu
                        nháp</button>
                    <button type="submit"
                        class="w-full py-2.5 bg-primary text-white font-bold rounded shadow hover:bg-primary/90 transition-colors">Công
                        bố biên bản</button>
                </div>
            </div>
        </form>
    </div>
@endsection
