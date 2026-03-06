@extends('layouts.admin')
@section('title', 'Xem Trước Kết Quả')

@section('content')
<div class="flex flex-col h-[calc(100vh-100px)]">
    
    {{-- Header --}}
    <div class="flex justify-between items-center mb-4 px-4 pt-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Kiểm tra dữ liệu Import</h1>
            <p class="text-sm text-slate-500">Chỉ các dòng hợp lệ mới được lưu vào hệ thống.</p>
        </div>
        
        <form action="{{ route('admin.academic_results.store_import') }}" method="POST">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semester_id }}">
            <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
            
            <div class="flex gap-2">
                <a href="{{ route('admin.academic_results.import') }}" class="px-4 py-2 border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 text-sm font-bold">
                    Quay lại
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-sm hover:bg-primary/90 text-sm font-bold shadow-md flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Lưu kết quả
                </button>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="flex-1 overflow-auto px-4 pb-4">
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">MSSV</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Họ và Tên</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">GPA (10)</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">GPA (4)</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase text-center">Xếp loại</th>
                        <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700 text-sm">
                    @foreach($previewData as $row)
                        <tr class="{{ $row['status'] == 'error' ? 'bg-red-50 dark:bg-red-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }} transition-colors">
                            <td class="px-4 py-3 font-medium {{ $row['status'] == 'error' ? 'text-red-600' : 'text-primary' }}">
                                {{ $row['mssv'] }}
                            </td>
                            <td class="px-4 py-3 dark:text-slate-300">
                                {{ $row['fullname'] }}
                                <div class="text-[10px] text-slate-400">{{ $row['class_code'] }}</div>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold text-slate-600">{{ $row['gpa_10'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-800 dark:text-slate-200">{{ $row['gpa_4'] }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase border 
                                    {{ $row['classification'] == 'Xuất sắc' ? 'bg-green-100 text-green-700 border-green-200' : 
                                      ($row['classification'] == 'Giỏi' ? 'bg-blue-100 text-blue-700 border-blue-200' : 
                                      ($row['classification'] == 'Khá' ? 'bg-sky-100 text-sky-700 border-sky-200' : 'bg-slate-100 text-slate-600 border-slate-200')) }}">
                                    {{ $row['classification'] }}
                                </span>
                            </td>
                            
                            <td class="px-4 py-3">
                                @if($row['status'] == 'valid')
                                    <span class="flex items-center gap-1 text-emerald-600 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">check_circle</span> Hợp lệ
                                    </span>
                                @else
                                    <span class="flex items-center gap-1 text-red-600 text-xs font-bold">
                                        <span class="material-symbols-outlined text-[16px]">cancel</span> {{ $row['message'] }}
                                    </span>
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