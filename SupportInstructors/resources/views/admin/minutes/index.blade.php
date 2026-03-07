@extends('layouts.admin')

@section('title', 'Biên bản Sinh hoạt lớp')

@section('content')
    <div class="max-w-[1400px] mx-auto flex flex-col gap-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Biên bản Sinh hoạt lớp</h1>
                <p class="text-slate-500 text-sm mt-1">Lưu trữ và quản lý các biên bản họp định kỳ, đột xuất và xét điểm rèn luyện.</p>
            </div>
            <a href="{{ route('admin.minutes.create') }}"
                class="flex items-center gap-2 px-4 py-2 bg-primary text-white text-sm font-bold rounded-lg hover:bg-primary/90 transition-colors shadow-lg shadow-primary/30">
                <span class="material-symbols-outlined !text-[20px]">add</span> Tạo biên bản mới
            </a>
        </div>

        {{-- MAIN LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

            {{-- SIDEBAR FILTERS --}}
            <div class="lg:col-span-1 flex flex-col gap-4">
                <div class="bg-white dark:bg-[#1e1e2d] p-5 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm sticky top-24">
                    
                    {{-- Lọc Năm học & Học kỳ --}}
                    <div class="mb-6">
                        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Thời gian</h3>
                        <div class="space-y-2">
                            {{-- Option Tất cả --}}
                            <a href="{{ route('admin.minutes.index') }}" 
                               class="flex items-center justify-between p-2 rounded-lg {{ !request('semester_id') && !request('academic_year') ? 'bg-primary/10 text-primary font-bold' : 'hover:bg-slate-50 text-slate-600' }}">
                                <span class="text-sm italic">Tất cả thời gian</span>
                                <span class="material-symbols-outlined text-[18px]">calendar_today</span>
                            </a>

                            {{-- Danh sách Năm học & Học kỳ --}}
                            @foreach($academicYears as $year => $semesters)
                                <div x-data="{ open: {{ request('academic_year') == $year ? 'true' : 'false' }} }">
                                    <button @click="open = !open" 
                                            class="w-full flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 text-slate-700 font-semibold transition-colors">
                                        <span class="text-sm">Năm học {{ $year }}</span>
                                        <span class="material-symbols-outlined transform transition-transform" :class="open ? 'rotate-180' : ''">expand_more</span>
                                    </button>
                                    
                                    <div x-show="open" x-collapse class="pl-4 mt-1 space-y-1 border-l-2 border-slate-100 ml-2">
                                        {{-- Link lọc theo cả năm --}}
                                        <a href="{{ route('admin.minutes.index', ['academic_year' => $year]) }}" 
                                           class="block p-1.5 text-xs {{ request('academic_year') == $year && !request('semester_id') ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' }}">
                                           • Xem tất cả năm {{ $year }}
                                        </a>
                                        {{-- Link lọc từng học kỳ --}}
                                        @foreach($semesters as $sem)
                                            <a href="{{ route('admin.minutes.index', ['semester_id' => $sem->id]) }}" 
                                               class="block p-1.5 text-xs {{ request('semester_id') == $sem->id ? 'text-primary font-bold' : 'text-slate-500 hover:text-primary' }}">
                                               • {{ $sem->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Lọc Trạng thái --}}
                    <div>
                        <h3 class="font-bold text-xs text-slate-400 uppercase tracking-wider mb-3">Trạng thái</h3>
                        <div class="flex flex-col gap-2">
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'published']) }}" 
                               class="px-3 py-2 rounded-lg text-xs font-bold border {{ request('status') == 'published' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                               Đã duyệt (Công bố)
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['status' => 'draft']) }}" 
                               class="px-3 py-2 rounded-lg text-xs font-bold border {{ request('status') == 'draft' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                               Chờ duyệt (Nháp)
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIST CONTENT --}}
            <div class="lg:col-span-3 flex flex-col gap-4">

                @if($minutes->isEmpty())
                    <div class="bg-white dark:bg-[#1e1e2d] p-10 rounded-xl border border-dashed border-slate-300 text-center">
                        <span class="material-symbols-outlined text-slate-300 text-5xl mb-3">inbox</span>
                        <p class="text-slate-500">Chưa có biên bản nào được tạo.</p>
                    </div>
                @else
                    @foreach($minutes as $minute)
                        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-xl p-5 shadow-sm hover:shadow-md transition-all group relative overflow-hidden">
                            
                            {{-- STATUS BADGE --}}
                            @if($minute->status === 'published')
                                <div class="absolute top-0 right-0 px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase rounded-bl-xl z-10">
                                    Đã duyệt
                                </div>
                            @else
                                <div class="absolute top-0 right-0 px-3 py-1 bg-orange-100 text-orange-700 text-[10px] font-bold uppercase rounded-bl-xl z-10">
                                    Chờ duyệt
                                </div>
                            @endif

                            <div class="flex flex-col sm:flex-row gap-5">
                                {{-- DATE BOX --}}
                                <div class="flex flex-row sm:flex-col items-center justify-center bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-lg w-full sm:w-20 h-14 sm:h-20 flex-shrink-0 text-center gap-2 sm:gap-0">
                                    <span class="text-xs font-bold text-slate-400 uppercase hidden sm:block">Tháng</span>
                                    {{-- Lấy tháng và năm từ held_at --}}
                                    <span class="text-xl sm:text-3xl font-bold text-slate-800 dark:text-white leading-none">
                                        {{ $minute->held_at ? $minute->held_at->format('m') : '--' }}
                                    </span>
                                    <span class="text-[12px] sm:text-[10px] font-bold text-slate-400">
                                        {{ $minute->held_at ? $minute->held_at->format('Y') : '--' }}
                                    </span>
                                </div>

                                {{-- INFO --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 pr-16">
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-white group-hover:text-primary transition-colors truncate" title="{{ $minute->title }}">
                                            {{ $minute->title }}
                                        </h3>
                                    </div>
                                    <div class="text-xs text-primary font-bold mb-2">
                                        {{ $minute->studentClass->code ?? 'Lớp ???' }} - {{ $minute->studentClass->name ?? '' }}
                                    </div>

                                    <div class="flex flex-wrap gap-x-6 gap-y-2 mt-2 text-xs font-medium text-slate-500 uppercase tracking-wide">
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined !text-[16px]">schedule</span> 
                                            {{ $minute->held_at ? $minute->held_at->format('H:i') : '--' }} - 
                                            {{ $minute->ended_at ? $minute->ended_at->format('H:i') : '--' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined !text-[16px]">location_on</span> 
                                            {{ $minute->location }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <span class="material-symbols-outlined !text-[16px]">group</span> 
                                            Vắng: {{ count($minute->absent_list ?? []) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <span class="material-symbols-outlined !text-[16px]">edit_document</span> 
                                Người tạo: <span class="font-bold">{{ $minute->creator->name ?? 'N/A' }}</span>
                            </div>
                            
                            {{-- ACTION BUTTONS --}}
                            <div class="flex gap-2 self-end sm:self-auto flex-wrap items-stretch">
                                {{-- Nút XEM --}}
                                <a href="{{ route('admin.minutes.show', $minute->id) }}"
                                    class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 transition-colors border border-slate-200">
                                    <span class="material-symbols-outlined !text-[16px]">visibility</span> Xem
                                </a>

                                {{-- Nút Tải Word --}}
                                <a href="{{ route('admin.minutes.export_word', $minute->id) }}" 
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-primary bg-primary/5 hover:bg-primary/10 transition-colors border border-primary/10">
                                    <span class="material-symbols-outlined !text-[16px]">download</span> Word
                                </a>
                                <a href="{{ route('admin.minutes.export_pdf', $minute->id) }}" 
                                class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors border border-red-100">
                                    <span class="material-symbols-outlined !text-[16px]">picture_as_pdf</span> PDF
                                </a>

                                @if($minute->status === 'draft')
                                    {{-- Nút Sửa / Kiểm duyệt --}}
                                    <a href="{{ route('admin.minutes.edit', $minute->id) }}"
                                        class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-orange-600 bg-orange-50 hover:bg-orange-100 transition-colors border border-orange-100">
                                        
                                        @if((Auth::user()->role_id ?? 0) == 1)
                                            <span class="material-symbols-outlined !text-[16px]">fact_check</span> Duyệt
                                        @else
                                            <span class="material-symbols-outlined !text-[16px]">edit</span> Sửa
                                        @endif
                                    </a>

                                    {{-- Nút XÓA (Đã Fix ID và CSS) --}}
                                    <form id="form-delete-minute-{{ $minute->id }}" action="{{ route('admin.minutes.destroy', $minute->id) }}" method="POST" class="contents">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete({{ $minute->id }})"
                                            class="flex items-center gap-1 px-3 py-1.5 rounded text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 transition-colors border border-red-100 cursor-pointer">
                                            <span class="material-symbols-outlined !text-[16px]">delete</span> Xóa
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
                
                <div class="mt-4">
                    {{ $minutes->links() }}
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection
<script>
    function confirmDelete(minuteId) {
        window.showConfirm(
            'Xóa biên bản',
            'Bạn có chắc chắn muốn xóa vĩnh viễn biên bản này không? <br><span class="text-xs text-red-500 italic">Hành động này không thể hoàn tác.</span>',
            function() {
                document.getElementById('form-delete-minute-' + minuteId).submit();
            },
            'danger'
        );
    }
</script>