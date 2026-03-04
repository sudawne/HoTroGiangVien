@extends('layouts.admin')
@section('title', 'Xem chi tiết biên bản')

@section('content')
<div class="max-w-[1200px] mx-auto pb-10">
    
    {{-- THANH CÔNG CỤ (Sẽ ẩn khi in) --}}
    <div class="flex items-center justify-between mb-6 bg-white dark:bg-[#1e1e2d] p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 no-print">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.minutes.index') }}" class="p-2 rounded-full hover:bg-slate-100 text-slate-500 transition-colors">
                <span class="material-symbols-outlined">arrow_back</span>
            </a>
            <div>
                <h1 class="text-xl font-bold text-slate-800 dark:text-white">Chi tiết biên bản</h1>
                @if($minute->status == 'published')
                    <span class="text-xs text-emerald-600 font-bold uppercase flex items-center gap-1">
                        <span class="material-symbols-outlined text-[14px]">lock</span> Đã duyệt & Công bố
                    </span>
                @else
                    <span class="text-xs text-orange-500 font-bold uppercase">Bản nháp / Chờ duyệt</span>
                @endif
            </div>
        </div>

        <div class="flex gap-2">
            @if($minute->status === 'draft')
                <a href="{{ route('admin.minutes.edit', $minute->id) }}" class="px-4 py-2 bg-orange-50 text-orange-600 font-bold rounded border border-orange-200 hover:bg-orange-100 flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">edit</span> Sửa
                </a>
                
                @if((Auth::user()->role_id ?? 0) == 1)
                    <form action="{{ route('admin.minutes.approve', $minute->id) }}" method="POST" class="inline">
                        @csrf 
                        @method('PUT')
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded shadow hover:bg-emerald-700 flex items-center gap-2">
                            <span class="material-symbols-outlined text-[18px]">check</span> Duyệt
                        </button>
                    </form>
                @endif
            @endif
            <a href="{{ route('admin.minutes.export_word', $minute->id) }}" 
                class="px-4 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">description</span> Tải file Word
            </a>
            {{-- Nút In --}}
            <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700 flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">print</span> In biên bản
            </button>
        </div>
    </div>

    {{-- KHUNG GIẤY A4 (Mô phỏng Word) --}}
    <div id="print-area" class="bg-white text-black p-[2cm] mx-auto shadow-lg text-[13px]" style="width: 210mm; min-height: 297mm; font-family: 'Times New Roman', Times, serif; line-height: 1.5;">
        {{-- HEADER: QUỐC HIỆU & TIÊU NGỮ --}}
        <div class="flex justify-between items-start mb-6">
            <div class="text-center w-1/2">
                <p class="uppercase m-0">TRƯỜNG ĐẠI HỌC KIÊN GIANG</p>
                <p class="font-bold uppercase m-0">KHOA THÔNG TIN TRUYỀN THÔNG</p>
                <div class="h-[1px] bg-black w-1/3 mx-auto mt-1"></div>
            </div>
            <div class="text-center w-1/2">
                <p class="font-bold uppercase m-0">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</p>
                <p class="font-bold m-0">Độc lập - Tự do - Hạnh phúc</p>
                <div class="h-[1px] bg-black w-1/2 mx-auto mt-1"></div>
            </div>
        </div>

        {{-- TIÊU ĐỀ --}}
        <div class="text-center mb-6">
            <h1 class="font-bold text-[16px] uppercase mb-1">BIÊN BẢN HỌP LỚP</h1>
            <p class="font-bold mb-1">{{ $minute->title }}</p>
            <p>Học kỳ: {{ $minute->semester->name ?? '...' }} &nbsp;Năm học: {{ $minute->semester->academic_year ?? '...' }}</p>
        </div>

        {{-- MỤC I --}}
        <div class="mb-4">
            <h3 class="font-bold uppercase">I. THỜI GIAN, ĐỊA ĐIỂM, THÀNH PHẦN THAM DỰ</h3>
            
            <div class="pl-4">
                <p class="font-bold">1. Thời gian, địa điểm</p>
                <ul class="list-none pl-4 space-y-1">
                    <li>- Buổi sinh hoạt diễn ra lúc: {{ $minute->held_at ? $minute->held_at->format('H \g\i\ờ i \p\h\ú\t, \n\g\à\y d \t\h\á\n\g m \n\ă\m Y') : '...' }}.</li>
                    <li>- Địa điểm: {{ $minute->location }}.</li>
                </ul>

                <p class="font-bold mt-2">2. Thành phần tham dự</p>
                <ul class="list-none pl-4 space-y-1">
                    <li>- Cố vấn học tập: {{ $minute->studentClass->advisor->user->name ?? '...' }}</li>
                    <li>- Lớp trưởng: {{ $minute->monitor->fullname ?? '...' }}</li>
                    <li>- Thư ký: {{ $minute->secretary->fullname ?? '...' }}</li>
                    <li>
                        - Tổng số sinh viên: {{ $minute->attendees_count + count($minute->absent_list ?? []) }}; 
                        Có mặt: {{ $minute->attendees_count }}; 
                        Vắng: {{ count($minute->absent_list ?? []) }}
                    </li>
                </ul>
            </div>
        </div>

        {{-- MỤC II --}}
        <div class="mb-4">
            <h3 class="font-bold uppercase">II. NỘI DUNG</h3>
            <div class="pl-4 text-justify mt-2">
                {!! $minute->content_discussions !!}
            </div>
        </div>

        {{-- MỤC III --}}
        <div class="mb-4">
            <h3 class="font-bold uppercase">III. KẾT LUẬN</h3>
            <div class="pl-4 text-justify mt-2">
                {!! $minute->content_conclusion !!}
            </div>
        </div>

        {{-- MỤC IV --}}
        <div class="mb-4">
            <h3 class="font-bold uppercase">IV. KIẾN NGHỊ</h3>
            <div class="pl-4 text-justify mt-2">
                {!! $minute->content_requests !!}
            </div>
        </div>

        {{-- KẾT THÚC --}}
        <div class="mb-8 pl-4 italic">
            Cuộc họp kết thúc vào lúc {{ $minute->ended_at ? $minute->ended_at->format('H \g\i\ờ i \p\h\ú\t') : '...' }} cùng ngày./.
        </div>

        {{-- CHỮ KÝ --}}
        <div class="flex justify-between text-center font-bold uppercase">
            <div class="w-1/2">
                <p>THƯ KÝ</p>
                <div class="h-24"></div>
                <p>{{ $minute->secretary->fullname ?? '...' }}</p>
            </div>
            <div class="w-1/2">
                <p>CỐ VẤN HỌC TẬP</p>
                <div class="h-24"></div>
                <p>{{ $minute->studentClass->advisor->user->name ?? '...' }}</p>
            </div>
        </div>

    </div>
</div>

<style>
    /* CSS dành riêng cho chế độ in */
    @media print {
        @page {
            size: A4;
            margin: 10mm;
        }
        body * {
            visibility: hidden;
        }
        #print-area, #print-area * {
            visibility: visible;
        }
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
            box-shadow: none;
            border: none;
        }
        /* Ẩn thanh công cụ */
        .no-print {
            display: none !important;
        }
        /* Đảm bảo nền trắng chữ đen */
        body {
            background: white;
            color: black;
        }
    }
</style>
@endsection