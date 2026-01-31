@extends('layouts.admin')
@section('title', 'Import Cảnh Báo Học Tập')

@section('content')
    {{-- Container mở rộng full màn hình --}}
    <div class="w-full px-4 py-4 h-[calc(100vh-80px)] flex flex-col">
        
        @if(!isset($previewData))
            {{-- FORM GIAI ĐOẠN 1: UPLOAD & PREVIEW --}}
            <form action="{{ route('admin.academic_warnings.preview') }}" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
                @csrf
        @else
            {{-- FORM GIAI ĐOẠN 2: CONFIRM STORE --}}
            <form action="{{ route('admin.academic_warnings.store') }}" method="POST" class="flex flex-col h-full">
                @csrf
                <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
                <input type="hidden" name="semester_id" value="{{ $semester_id }}">
        @endif

        {{-- === PHẦN 1: THANH CÔNG CỤ (TOOLBAR) === --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm mb-4 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-20">
            
            {{-- Nhóm bên trái: Quay lại & Học kỳ --}}
            <div class="flex items-center gap-4 flex-1">
                <a href="{{ route('admin.academic_warnings.index') }}" 
                   class="flex items-center gap-2 text-slate-500 hover:text-red-600 transition-colors font-medium text-sm">
                    <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
                    Hủy bỏ & Trở về
                </a>

                <div class="h-6 w-px bg-slate-300 dark:bg-slate-700"></div>

                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 !text-[20px]">calendar_month</span>
                    <select name="semester_id" class="bg-slate-50 dark:bg-slate-800 border-none text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-2 pr-8 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ (isset($semester_id) && $semester_id == $sem->id) ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Nhóm bên phải: Các nút thao tác --}}
            <div class="flex items-center gap-3">
                @if(!isset($previewData))
                    {{-- Trạng thái 1: Chưa có dữ liệu --}}
                    
                    {{-- Nút chọn file Excel (Style giả nút) --}}
                    <label for="file-upload" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium text-sm transition-colors border border-slate-200 dark:border-slate-600">
                        <span class="material-symbols-outlined !text-[20px] text-green-600">table_view</span>
                        <span id="toolbar-filename">Chọn file Excel</span>
                        <input id="file-upload" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)" />
                    </label>

                    {{-- Nút Xem dữ liệu (Submit Form) --}}
                    <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm shadow-indigo-500/30">
                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                        Xem dữ liệu
                    </button>
                @else
                    {{-- Trạng thái 2: Đã có dữ liệu (Preview) --}}
                    <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium border border-blue-100 dark:border-blue-800 flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[18px]">description</span>
                        File: {{ $selected_file_name ?? 'Data.xlsx' }}
                    </div>
                    
                    {{-- Nút Reset để chọn lại --}}
                    <a href="{{ route('admin.academic_warnings.import') }}" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-medium text-sm">
                        Chọn lại
                    </a>
                @endif
            </div>
        </div>

        {{-- === PHẦN 2: KHU VỰC NỘI DUNG (DƯỚI) === --}}
        <div class="flex-1 bg-white dark:bg-surface-dark rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative flex flex-col">
            
            @if(!isset($previewData))
                {{-- VIEW 1: DROPZONE UPLOAD --}}
                <div class="flex-1 flex flex-col items-center justify-center p-10 border-4 border-dashed border-slate-100 dark:border-slate-800 m-4 rounded-xl bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group cursor-pointer" onclick="document.getElementById('file-upload').click()">
                    <div class="bg-white dark:bg-slate-700 p-6 rounded-full shadow-sm mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-500 group-hover:text-primary transition-colors">cloud_upload</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200 mb-2">Tải lên danh sách cảnh báo</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Kéo thả file vào đây hoặc bấm nút "Chọn file Excel" ở trên</p>
                    <div class="flex gap-4 text-xs text-slate-400 font-mono">
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.XLSX</span>
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.XLS</span>
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.CSV</span>
                    </div>
                </div>

            @else
                {{-- VIEW 2: BẢNG DỮ LIỆU --}}
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">MSSV</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Họ tên</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Lớp</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Khoa</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">ĐTB</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">TC Rớt</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Mức CB</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-right">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($previewData as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors {{ !$row['exists'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}" id="row-{{ $row['mssv'] }}">
                                    <td class="p-4 font-medium {{ !$row['exists'] ? 'text-red-600' : 'text-primary' }}">{{ $row['mssv'] }}</td>
                                    <td class="p-4 font-medium dark:text-slate-300">{{ $row['fullname'] }}</td>
                                    <td class="p-4 text-slate-500">{{ $row['class_code'] }}</td>
                                    <td class="p-4 text-slate-500">{{ $row['department'] }}</td>
                                    <td class="p-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['gpa_term'] }}</td>
                                    <td class="p-4 text-center {{ $row['credits_failed'] > 0 ? 'text-red-500 font-bold' : '' }}">{{ $row['credits_failed'] }}</td>
                                    <td class="p-4">
                                        @if($row['warning_level'] == 1) <span class="badge-yellow">Lần 1</span>
                                        @elseif($row['warning_level'] == 2) <span class="badge-orange">Lần 2</span>
                                        @elseif($row['warning_level'] >= 3) <span class="badge-red">Thôi học</span>
                                        @else <span class="badge-gray">Khác</span> @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($row['exists'])
                                            <span class="text-green-600 font-bold text-xs flex items-center justify-end gap-1"><span class="material-symbols-outlined text-sm">check</span> Hợp lệ</span>
                                        @else
                                            <button type="button" onclick="quickAddStudent('{{ $row['mssv'] }}', '{{ $row['fullname'] }}', '{{ $row['dob'] }}')" class="btn-xs-red">
                                                + Thêm SV
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- NÚT XÁC NHẬN Ở DƯỚI CÙNG (Sticky Bottom) --}}
                <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <div class="text-sm text-slate-500">
                        Đang xem trước <span class="font-bold text-slate-900 dark:text-white">{{ count($previewData) }}</span> dòng dữ liệu.
                    </div>
                    <button type="submit" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-green-600/20 transform hover:-translate-y-0.5 transition-all">
                        <span class="material-symbols-outlined">save</span>
                        Xác nhận & Lưu vào hệ thống
                    </button>
                </div>
            @endif

        </div>

        </form> {{-- Đóng thẻ Form tương ứng với If/Else --}}
    </div>

    {{-- SCRIPT JAVASCRIPT HỖ TRỢ --}}
    <script>
        // Cập nhật tên file trên thanh Toolbar khi chọn
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('toolbar-filename');
            if (input.files && input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
                fileNameSpan.classList.add('text-primary', 'font-bold');
            } else {
                fileNameSpan.textContent = 'Chọn file Excel';
                fileNameSpan.classList.remove('text-primary', 'font-bold');
            }
        }

        // Hàm thêm nhanh sinh viên (Giữ nguyên logic cũ)
        function quickAddStudent(mssv, fullname, dob) {
            if(!confirm('Thêm sinh viên ' + fullname + ' (' + mssv + ')?')) return;
            const btn = event.currentTarget;
            btn.innerHTML = '...'; btn.disabled = true;

            fetch('{{ route("admin.academic_warnings.quick_add_student") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ mssv, fullname, dob })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    const row = document.getElementById('row-' + mssv);
                    row.classList.remove('bg-red-50/50', 'dark:bg-red-900/10'); row.classList.add('bg-green-50/30');
                    btn.parentElement.innerHTML = '<span class="text-green-600 font-bold text-xs">Đã thêm</span>';
                } else { alert(data.message); btn.disabled = false; btn.innerHTML = '+ Thêm SV'; }
            });
        }
    </script>

    {{-- STYLE NHỎ CHO BUTTON BADGE --}}
    <style>
        .badge-yellow { @apply px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200; }
        .badge-orange { @apply px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200; }
        .badge-red { @apply px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 border border-red-200; }
        .badge-gray { @apply px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200; }
        .btn-xs-red { @apply inline-flex items-center gap-1 bg-white border border-red-200 hover:bg-red-50 text-red-600 px-3 py-1 rounded text-xs font-bold transition-colors shadow-sm; }
    </style>
@endsection