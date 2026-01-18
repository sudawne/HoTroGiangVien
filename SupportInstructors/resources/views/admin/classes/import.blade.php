@extends('layouts.admin')
@section('title', 'Import Sinh viên - ' . $class->code)

@section('content')
    <div class="w-full px-4 py-6">

        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.classes.index') }}"
                class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 shadow-sm">
                <span class="material-symbols-outlined !text-[20px] block">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Import Danh sách Sinh viên</h1>
                <p class="text-sm text-slate-500">Lớp: <span class="font-bold text-primary">{{ $class->code }}</span> -
                    {{ $class->name }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm p-6">

            {{-- TRẠNG THÁI 1: CHƯA CÓ DỮ LIỆU PREVIEW -> HIỆN FORM UPLOAD --}}
            @if (!isset($previewData))
                <form action="{{ route('admin.classes.import.preview') }}" method="POST" enctype="multipart/form-data"
                    class="max-w-xl mx-auto">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->id }}">

                    <div
                        class="border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg p-8 text-center hover:bg-slate-50 transition-colors">
                        <div class="mb-4 text-slate-400">
                            <span class="material-symbols-outlined !text-[64px]">upload_file</span>
                        </div>
                        <h3 class="text-lg font-bold text-slate-700 dark:text-white mb-2">Tải lên danh sách sinh viên</h3>
                        <p class="text-sm text-slate-500 mb-6">Chấp nhận file .xlsx, .xls, .csv. File mẫu theo định dạng nhà
                            trường.</p>

                        <input type="file" name="file" required
                            class="block w-full text-sm text-slate-500
                        file:mr-4 file:py-2.5 file:px-6
                        file:rounded-full file:border-0
                        file:text-sm file:font-semibold
                        file:bg-primary file:text-white
                        hover:file:bg-primary/90 cursor-pointer mx-auto" />
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button type="submit"
                            class="px-6 py-2.5 bg-primary text-white font-bold rounded-sm shadow hover:bg-primary/90 transition-all flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[20px]">visibility</span> Xem trước dữ liệu
                        </button>
                    </div>
                </form>

                {{-- TRẠNG THÁI 2: ĐÃ CÓ DỮ LIỆU PREVIEW -> HIỆN BẢNG & NÚT XÁC NHẬN --}}
            @else
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-bold text-lg text-slate-800 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-green-600">table_view</span>
                            Dữ liệu đọc được ({{ count($previewData) }} dòng)
                        </h3>
                        <div class="flex gap-2">
                            {{-- Nút Hủy / Làm lại --}}
                            <a href="{{ route('admin.classes.import', $class->id) }}"
                                class="px-4 py-2 border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 font-semibold text-sm">
                                Chọn file khác
                            </a>

                            {{-- Form Xác nhận Import --}}
                            <form action="{{ route('admin.classes.import.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="class_id" value="{{ $class->id }}">
                                <input type="hidden" name="temp_path" value="{{ $tempPath }}">
                                <button type="submit"
                                    class="px-4 py-2 bg-green-600 text-white rounded-sm hover:bg-green-700 font-bold text-sm shadow flex items-center gap-2">
                                    <span class="material-symbols-outlined !text-[18px]">check_circle</span> Xác nhận Import
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-slate-200 rounded-sm">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-slate-100 text-slate-700 font-bold uppercase text-xs">
                                <tr>
                                    <th class="px-4 py-3">STT</th>
                                    <th class="px-4 py-3">Mã SV</th>
                                    <th class="px-4 py-3">Họ và Tên</th>
                                    <th class="px-4 py-3">Ngày sinh</th>
                                    <th class="px-4 py-3">Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($previewData as $index => $row)
                                    {{-- Kiểm tra dòng có dữ liệu không --}}
                                    @if (isset($row[1]) && !empty($row[1]))
                                        <tr class="hover:bg-slate-50">
                                            <td class="px-4 py-2 text-slate-500">{{ $index + 1 }}</td>
                                            <td class="px-4 py-2 font-mono font-bold text-primary">{{ $row[1] }}</td>
                                            <td class="px-4 py-2 font-medium">{{ $row[2] }} {{ $row[3] }}</td>
                                            <td class="px-4 py-2">{{ $row[5] ?? '--' }}</td>
                                            <td class="px-4 py-2">
                                                <span
                                                    class="px-2 py-0.5 rounded text-[10px] bg-slate-100 border border-slate-200">
                                                    {{ $row[6] ?? '---' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
@endsection
