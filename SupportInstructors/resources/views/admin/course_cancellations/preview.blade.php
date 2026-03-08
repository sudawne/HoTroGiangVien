@extends('layouts.admin')
@section('title', 'Xem trước Import')

@section('content')
<div class="w-full px-4 py-6 h-[calc(100vh-80px)] flex flex-col">
    <form action="{{ route('admin.course_cancellations.store_import') }}" method="POST" class="flex flex-col h-full">
        @csrf
        <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
        <input type="hidden" name="semester_id" value="{{ $semester_id }}">

        {{-- Header Form --}}
        <div class="flex justify-between items-center mb-4 shrink-0">
            <div>
                <h1 class="text-xl font-bold text-slate-800">Xác nhận Import</h1>
                <p class="text-sm text-slate-500">Chỉ những dòng có đủ <span class="text-emerald-600 font-bold">SV</span> và <span class="text-emerald-600 font-bold">Môn học</span> mới được lưu.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.course_cancellations.import') }}" class="px-4 py-2 border rounded hover:bg-slate-50">Hủy</a>
                <button type="submit" class="px-6 py-2 bg-primary text-white rounded hover:bg-primary/90 font-bold shadow-sm">Lưu Dữ Liệu</button>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-white border rounded-lg shadow-sm overflow-hidden flex-1 relative">
            <div class="absolute inset-0 overflow-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead class="bg-slate-100 border-b sticky top-0 z-10 shadow-sm">
                        <tr>
                            <th class="px-4 py-3">MSSV</th>
                            <th class="px-4 py-3">Họ tên (Excel)</th>
                            <th class="px-4 py-3">Mã Môn</th>
                            <th class="px-4 py-3">Tên Môn (Excel)</th>
                            <th class="px-4 py-3 text-center">Kiểm tra</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($previewData as $row)
                            <tr class="{{ ($row['student_exists'] && $row['subject_exists']) ? 'bg-white' : 'bg-red-50' }}">
                                <td class="px-4 py-2 font-mono {{ !$row['student_exists'] ? 'text-red-600 font-bold' : '' }}">
                                    {{ $row['student_code'] }}
                                </td>
                                <td class="px-4 py-2">{{ $row['student_name'] }}</td>
                                <td class="px-4 py-2 font-mono {{ !$row['subject_exists'] ? 'text-red-600 font-bold' : '' }}">
                                    {{ $row['subject_code'] }}
                                </td>
                                <td class="px-4 py-2">{{ $row['subject_name'] }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if(!$row['student_exists'])
                                        <span class="inline-block px-2 py-1 text-[10px] bg-red-100 text-red-700 rounded border border-red-200">Sai MSSV</span>
                                    @endif
                                    
                                    @if(!$row['subject_exists'])
                                        <span class="inline-block px-2 py-1 text-[10px] bg-orange-100 text-orange-700 rounded border border-orange-200">Sai Mã Môn</span>
                                    @endif

                                    @if($row['student_exists'] && $row['subject_exists'])
                                        <span class="material-symbols-outlined text-emerald-600 !text-[20px]">check_circle</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </form>
</div>
@endsection