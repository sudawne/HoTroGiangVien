@forelse($results as $index => $item)
    <tr class="hover:bg-slate-50 transition-colors">
        <td class="px-5 py-3 text-center text-slate-500 font-medium">
            {{ ($results->currentPage() - 1) * $results->perPage() + $index + 1 }}
        </td>
        <td class="px-5 py-3">
            <div class="font-bold text-slate-800">{{ $item->student->fullname }}</div>
            <div class="text-xs text-slate-500 font-mono mt-0.5">
                {{ $item->student->student_code }} - <span
                    class="text-blue-600 font-semibold">{{ $item->student->class->code ?? 'N/A' }}</span>
            </div>
        </td>
        <td class="px-5 py-3 text-center font-semibold text-slate-700">
            {{ number_format($item->gpa_10, 2) }}
        </td>
        <td class="px-5 py-3 text-center font-bold text-primary">
            {{ number_format($item->gpa_4, 2) }}
        </td>
        <td class="px-5 py-3 text-center font-medium">
            {{ $item->accumulated_credits }}
        </td>
        <td class="px-5 py-3 text-center">
            @php
                $color = match ($item->classification) {
                    'Xuất sắc', 'Giỏi' => 'bg-green-100 text-green-700 border-green-200',
                    'Khá' => 'bg-blue-100 text-blue-700 border-blue-200',
                    'Trung bình' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'Yếu', 'Kém' => 'bg-red-100 text-red-700 border-red-200',
                    default => 'bg-gray-100 text-gray-600 border-gray-200',
                };
            @endphp
            <span class="px-2 py-1 text-[11px] font-bold uppercase border rounded {{ $color }}">
                {{ $item->classification ?? 'Chưa xét' }}
            </span>
        </td>
        <td
            class="px-5 py-3 text-center font-bold {{ $item->training_point >= 80 ? 'text-green-600' : 'text-slate-600' }}">
            {{ $item->training_point ?? '--' }}
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-5 py-8 text-center text-slate-500 italic">Chưa có dữ liệu kết quả học tập cho kỳ
            này.</td>
    </tr>
@endforelse
