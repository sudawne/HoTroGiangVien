{{-- resources/views/admin/training_points/partials/table_rows.blade.php --}}

@forelse($trainingPoints as $index => $point)
    @php
        // Logic tính màu sắc và xếp loại
        $score = $point->final_score;
        $rankName = 'Chưa xét';
        $rankClass = 'bg-slate-100 text-slate-500 border-slate-200'; // Mặc định xám
        
        if ($score !== null) {
            if ($score >= 90) {
                $rankName = 'Xuất sắc';
                $rankClass = 'bg-emerald-100 text-emerald-700 border-emerald-200';
            } elseif ($score >= 80) {
                $rankName = 'Tốt';
                $rankClass = 'bg-blue-100 text-blue-700 border-blue-200';
            } elseif ($score >= 65) {
                $rankName = 'Khá';
                $rankClass = 'bg-sky-100 text-sky-700 border-sky-200';
            } elseif ($score >= 50) {
                $rankName = 'Trung bình';
                $rankClass = 'bg-orange-100 text-orange-700 border-orange-200';
            } else {
                $rankName = 'Yếu';
                $rankClass = 'bg-red-100 text-red-700 border-red-200';
            }
        }
    @endphp

    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors border-b border-slate-50 dark:border-slate-800">
        {{-- STT: Tính toán dựa trên phân trang --}}
        <td class="px-6 py-4 text-center text-slate-400 font-mono text-xs">
            {{ $trainingPoints->firstItem() + $index }}
        </td>
        
        {{-- Cột Sinh viên --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                {{-- Avatar chữ cái --}}
                <div class="w-8 h-8 rounded-sm bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-500 border border-slate-200 dark:border-slate-600">
                    {{ substr($point->student->fullname ?? 'U', 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $point->student->fullname ?? 'N/A' }}</div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $point->student->student_code ?? '---' }}</div>
                </div>
            </div>
        </td>

        {{-- Điểm Tự ĐG --}}
        <td class="px-6 py-4 text-center text-slate-500 font-mono text-sm">
            {{ $point->self_score ?? '--' }}
        </td>

        {{-- Điểm Lớp ĐG --}}
        <td class="px-6 py-4 text-center text-slate-500 font-mono text-sm">
            {{ $point->class_score ?? '--' }}
        </td>

        {{-- Điểm Khoa Duyệt --}}
        <td class="px-6 py-4 text-center">
            <span class="font-bold text-slate-800 dark:text-slate-300 font-mono text-base">
                {{ $point->advisor_score ?? '--' }}
            </span>
        </td>

        {{-- Xếp loại --}}
        <td class="px-6 py-4 text-center">
            <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-sm text-[10px] font-bold uppercase tracking-wide border {{ $rankClass }}">
                {{ $rankName }}
            </span>
        </td>

        {{-- Thao tác --}}
        <td class="px-6 py-4 text-right">
            <a href="#" class="text-slate-400 hover:text-primary transition-colors p-2 rounded-sm hover:bg-slate-100 inline-block" title="Chỉnh sửa">
                <span class="material-symbols-outlined !text-[18px]">edit</span>
            </a>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-12 text-center text-slate-500 italic bg-slate-50/30">
            <div class="flex flex-col items-center justify-center gap-2">
                <span class="material-symbols-outlined text-slate-300 !text-[32px]">manage_search</span>
                <p>Không tìm thấy kết quả phù hợp.</p>
            </div>
        </td>
    </tr>
@endforelse

{{-- Quan trọng: Trả về cả phân trang để AJAX cập nhật nút next/prev --}}
<tr>
    <td colspan="7" class="px-6 py-4 border-t border-slate-100 dark:border-slate-800">
        {{ $trainingPoints->links() }} 
    </td>
</tr>