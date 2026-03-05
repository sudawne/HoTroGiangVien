@extends('layouts.admin')
@section('title', 'Xem Trước Dữ Liệu')

@section('content')
<div class="flex flex-col h-[calc(100vh-100px)]">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4 px-4 pt-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Bước 2: Kiểm tra dữ liệu</h1>
            <p class="text-sm text-slate-500">Chỉ những dòng <span class="text-emerald-600 font-bold">Hợp lệ</span> mới được lưu vào hệ thống.</p>
        </div>
        
        <form action="{{ route('admin.training_points.store_import') }}" method="POST">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semester_id }}">
            <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
            
            <div class="flex gap-2">
                <a href="{{ route('admin.training_points.import') }}" class="px-4 py-2 border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 text-sm font-bold">
                    Quay lại
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-sm hover:bg-primary/90 text-sm font-bold shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Lưu dữ liệu hợp lệ
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto px-4 pb-4">
        <div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">MSSV</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Họ và Tên</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">Tự ĐG</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">Lớp ĐG</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @foreach($previewData as $row)
                        @php
                            $bgClass = 'hover:bg-slate-50';
                            $textClass = 'text-slate-700';
                            
                            if ($row['status'] == 'error') {
                                $bgClass = 'bg-red-50 hover:bg-red-100';
                                $textClass = 'text-red-700';
                            } elseif ($row['status'] == 'warning') {
                                $bgClass = 'bg-orange-50 hover:bg-orange-100';
                                $textClass = 'text-orange-700';
                            }
                        @endphp

                        <tr class="{{ $bgClass }} transition-colors">
                            <td class="px-4 py-3 font-medium {{ $textClass }}">
                                {{ $row['mssv'] }}
                            </td>
                            <td class="px-4 py-3 {{ $textClass }}">
                                {{ $row['fullname'] }}
                            </td>
                            <td class="px-4 py-3 text-center text-slate-600">{{ $row['self_score'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800">{{ $row['class_score'] }}</td>
                            
                            <td class="px-4 py-3">
                                @if($row['status'] == 'valid')
                                    <span class="flex items-center gap-1 text-emerald-600 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span> Hợp lệ
                                    </span>
                                @elseif($row['status'] == 'warning')
                                    <div class="flex flex-col">
                                        <span class="flex items-center gap-1 text-orange-600 text-xs font-bold">
                                            <span class="material-symbols-outlined text-[16px]">warning</span> Cảnh báo
                                        </span>
                                        <span class="text-[10px] text-orange-500 italic">{{ $row['message'] }}</span>
                                    </div>
                                @else
                                    <div class="flex flex-col">
                                        <span class="flex items-center gap-1 text-red-600 text-xs font-bold">
                                            <span class="material-symbols-outlined text-[16px]">cancel</span> Không thể thêm
                                        </span>
                                        <span class="text-[10px] text-red-500 italic">{{ $row['message'] }}</span>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection