@forelse($results as $index => $result)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
        {{-- STT --}}
        <td class="px-6 py-4 text-center text-slate-400 font-mono text-xs">
            {{ $results->firstItem() + $index }}
        </td>

        {{-- Sinh viên --}}
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-sm bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-xs font-bold text-indigo-600 dark:text-indigo-400 border border-indigo-100">
                    {{ substr(strtoupper($result->student->fullname ?? 'U'), 0, 1) }}
                </div>
                <div>
                    <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $result->student->fullname ?? 'N/A' }}</div>
                    <div class="text-xs text-slate-500 font-mono mt-0.5">{{ $result->student->student_code ?? '---' }}</div>
                </div>
            </div>
        </td>

        {{-- Lớp --}}
        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
            {{ $result->student->studentClass->code ?? 'N/A' }}
        </td>

        {{-- GPA 10 --}}
        <td class="px-6 py-4 text-center">
            <span class="text-slate-700 dark:text-slate-300 font-semibold font-mono">
                {{ number_format($result->gpa_10, 2) }}
            </span>
        </td>

        {{-- GPA 4 --}}
        <td class="px-6 py-4 text-center">
            <span class="bg-slate-100 text-slate-700 px-2 py-1 rounded text-xs font-bold font-mono border border-slate-200">
                {{ number_format($result->gpa_4, 2) }}
            </span>
        </td>

        {{-- Xếp loại (Badge màu sắc) --}}
        <td class="px-6 py-4 text-center">
            @php
                $rankColor = match($result->classification) {
                    'Xuất sắc', 'Giỏi' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                    'Khá' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'Trung bình' => 'bg-orange-100 text-orange-700 border-orange-200',
                    'Yếu', 'Kém', 'Học lại' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-slate-100 text-slate-500 border-slate-200'
                };
            @endphp
            <span class="px-2.5 py-0.5 rounded-sm text-[11px] uppercase font-bold border {{ $rankColor }}">
                {{ $result->classification ?? 'Chưa xét' }}
            </span>
        </td>

        {{-- Tác vụ --}}
        <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
                <a href="#" class="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-blue-600 transition-colors" title="Xem chi tiết">
                    <span class="material-symbols-outlined !text-[18px]">visibility</span>
                </a>
                <a href="#" class="p-1.5 hover:bg-slate-100 rounded text-slate-500 hover:text-orange-600 transition-colors" title="Chỉnh sửa">
                    <span class="material-symbols-outlined !text-[18px]">edit</span>
                </a>
            </div>
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