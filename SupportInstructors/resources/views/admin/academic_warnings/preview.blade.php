@extends('layouts.admin')
@section('title', 'Xem Trước Dữ Liệu')

@section('content')
<div class="flex flex-col h-[calc(100vh-100px)]">
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Kiểm tra dữ liệu import</h1>
            <p class="text-sm text-slate-500">Vui lòng rà soát sinh viên chưa có trong hệ thống (được tô đỏ).</p>
        </div>
        <form action="{{ route('admin.academic_warnings.store') }}" method="POST">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semester_id }}">
            <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
            <div class="flex gap-2">
                <a href="{{ route('admin.academic_warnings.import') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium">Quay lại</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-md">
                    Xác nhận Import
                </button>
            </div>
        </form>
    </div>

    <div class="flex-1 bg-white dark:bg-surface-dark rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
        <div class="overflow-auto flex-1">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="p-3 font-semibold text-slate-600 border-b">MSSV</th>
                        <th class="p-3 font-semibold text-slate-600 border-b">Họ tên</th>
                        <th class="p-3 font-semibold text-slate-600 border-b text-center">ĐTB (HK)</th>
                        <th class="p-3 font-semibold text-slate-600 border-b text-center">TB Tích lũy</th>
                        <th class="p-3 font-semibold text-slate-600 border-b text-center">TC Rớt</th>
                        <th class="p-3 font-semibold text-slate-600 border-b">Lý do</th>
                        <th class="p-3 font-semibold text-slate-600 border-b">Mức CB</th>
                        <th class="p-3 font-semibold text-slate-600 border-b text-right">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($previewData as $index => $row)
                        <tr class="transition-colors hover:bg-slate-50 {{ !$row['exists'] ? 'bg-red-50 dark:bg-red-900/10' : '' }}" id="row-{{ $row['mssv'] }}">
                            <td class="p-3 font-medium {{ !$row['exists'] ? 'text-red-600' : 'text-primary' }}">{{ $row['mssv'] }}</td>
                            <td class="p-3 font-medium">{{ $row['fullname'] }}</td>
                            <td class="p-3 text-center">{{ $row['gpa_term'] }}</td>
                            <td class="p-3 text-center">{{ $row['gpa_cumulative'] }}</td>
                            <td class="p-3 text-center {{ $row['credits_failed'] > 0 ? 'text-red-500 font-bold' : '' }}">{{ $row['credits_failed'] }}</td>
                            <td class="p-3 text-xs text-slate-500 max-w-[200px] truncate" title="{{ $row['reason'] }}">{{ $row['reason'] }}</td>
                            <td class="p-3">
                                @if($row['warning_level'] == 1)
                                    <span class="px-2 py-0.5 rounded text-xs bg-yellow-100 text-yellow-700">Lần 1</span>
                                @elseif($row['warning_level'] == 2)
                                    <span class="px-2 py-0.5 rounded text-xs bg-orange-100 text-orange-700">Lần 2</span>
                                @elseif($row['warning_level'] >= 3)
                                    <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700">Thôi học</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-xs bg-slate-100 text-slate-600">Theo dõi</span>
                                @endif
                            </td>
                            <td class="p-3 text-right">
                                @if($row['exists'])
                                    <span class="flex items-center justify-end gap-1 text-green-600 text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        Hợp lệ
                                    </span>
                                @else
                                    <button onclick="quickAddStudent('{{ $row['mssv'] }}', '{{ $row['fullname'] }}', '{{ $row['dob'] }}')" 
                                            class="inline-flex items-center gap-1 bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors shadow-sm">
                                        <span class="material-symbols-outlined text-sm">person_add</span>
                                        Thêm vào CSDL
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function quickAddStudent(mssv, fullname, dob) {
        if(!confirm('Xác nhận thêm nhanh sinh viên ' + fullname + ' (' + mssv + ') vào hệ thống?')) return;

        const btn = event.currentTarget;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">sync</span> Đang thêm...';
        btn.disabled = true;

        fetch('{{ route("admin.academic_warnings.quick_add_student") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ mssv, fullname, dob })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI thành công
                const row = document.getElementById('row-' + mssv);
                row.classList.remove('bg-red-50', 'dark:bg-red-900/10');
                row.classList.add('bg-green-50');
                
                // Đổi nút thành check xanh
                const cell = btn.parentElement;
                cell.innerHTML = `
                    <span class="flex items-center justify-end gap-1 text-green-600 text-xs font-bold animate-pulse">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Đã thêm mới
                    </span>
                `;
            } else {
                alert('Lỗi: ' + data.message);
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi kết nối server');
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }
</script>
@endsection