{{-- resources/views/admin/academic_warnings/partials/table_rows.blade.php --}}

@forelse($warnings as $warning)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30 transition-colors">
        <td class="px-6 py-4 text-sm font-medium text-primary font-mono">
            {{ $warning->student->student_code }}
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-sm bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-xs font-bold text-slate-600 dark:text-slate-300">
                    {{ substr(strtoupper($warning->student->fullname), 0, 1) }}
                </div>
                <div class="text-sm font-medium dark:text-slate-200">
                    {{ $warning->student->fullname }}
                </div>
            </div>
        </td>
        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-400">
            {{ $warning->student->class->code ?? 'N/A' }}
        </td>
        <td class="px-6 py-4 text-sm text-center font-bold {{ $warning->gpa_term < 2.0 ? 'text-red-500' : 'text-slate-700' }}">
            {{ $warning->gpa_term }}
        </td>
        <td class="px-6 py-4 text-sm text-center text-slate-600">
            {{ $warning->credits_owed }}
        </td>
        <td class="px-6 py-4">
            @if($warning->warning_level == 1)
                <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-yellow-100 text-yellow-700">Mức 1</span>
            @elseif($warning->warning_level == 2)
                <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-orange-100 text-orange-700">Mức 2</span>
            @elseif($warning->warning_level >= 3)
                <span class="px-2.5 py-0.5 rounded-sm text-xs font-bold bg-red-200 text-red-800">Buộc thôi học</span>
            @else
                <span class="px-2.5 py-0.5 rounded-sm text-xs font-semibold bg-slate-100 text-slate-600">Khác</span>
            @endif
        </td>
        <td class="px-6 py-4 text-xs text-slate-500 max-w-[150px] truncate" title="{{ $warning->reason }}">
            {{ $warning->reason }}
        </td>
        <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
                <button class="p-1.5 text-slate-400 hover:text-primary transition-colors" title="Xem chi tiết">
                    <span class="material-symbols-outlined !text-[15px]">visibility</span>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="8" class="px-6 py-8 text-center text-slate-500">
            Không tìm thấy dữ liệu nào phù hợp.
        </td>
    </tr>
@endforelse

{{-- Thêm 1 dòng ẩn để chứa Pagination Links nếu cần update cả phân trang bằng JS --}}
<tr id="pagination-links" class="hidden">
    <td>{{ $warnings->links() }}</td>
</tr>