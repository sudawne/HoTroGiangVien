@extends('layouts.admin')
@section('content')
<div class="max-w-5xl mx-auto p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Chi tiết biên bản</h1>
        
        <div class="flex gap-2">
            {{-- Nút SỬA: Chỉ hiện nếu status là draft --}}
            @if($minute->status === 'draft')
                <a href="{{ route('admin.minutes.edit', $minute->id) }}" 
                   class="px-4 py-2 bg-orange-500 text-white rounded font-bold hover:bg-orange-600 transition">
                    Chỉnh sửa biên bản
                </a>

                {{-- Nút DUYỆT dành cho Admin --}}
                @if((Auth::user()->role_id ?? 0) == 1)
                    <form action="{{ route('admin.minutes.approve', $minute->id) }}" method="POST">
                        @csrf @method('PATCH')
                        <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded font-bold">Duyệt & Công bố</button>
                    </form>
                @endif
            @endif
            
            <button class="px-4 py-2 bg-slate-200 rounded font-bold">Xuất Word</button>
        </div>
    </div>

    <div class="bg-white p-10 rounded-xl shadow-sm border border-slate-200">
        <div class="text-center mb-8">
            <h2 class="text-xl font-bold uppercase">{{ $minute->title }}</h2>
            <p class="italic">Lớp: {{ $minute->studentClass->name }} - Học kỳ: {{ $minute->semester->name }}</p>
        </div>

        <div class="space-y-6">
            <section>
                <h3 class="font-bold">I. THÔNG TIN CHUNG</h3>
                <p>- Thời gian: {{ $minute->held_at->format('H:i d/m/Y') }}</p>
                <p>- Địa điểm: {{ $minute->location }}</p>
                <p>- Chủ trì: {{ $minute->monitor->fullname ?? 'N/A' }}</p>
                <p>- Thư ký: {{ $minute->secretary->fullname ?? 'N/A' }}</p>
            </section>

            <section>
                <h3 class="font-bold">II. NỘI DUNG CUỘC HỌP</h3>
                <div class="pl-4 whitespace-pre-line">{{ $minute->content_discussions }}</div>
            </section>

            <section>
                <h3 class="font-bold">III. KẾT LUẬN</h3>
                <div class="pl-4 whitespace-pre-line">{{ $minute->content_conclusion }}</div> section>
        </div>
    </div>
</div>
@endsection