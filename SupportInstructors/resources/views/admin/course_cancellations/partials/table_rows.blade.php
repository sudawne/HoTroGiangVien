@forelse($cancellations as $item)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
        <td class="px-6 py-4">
            <div class="font-bold text-slate-800 dark:text-white text-sm">{{ $item->student->fullname ?? 'N/A' }}</div>
            <div class="text-xs text-primary font-mono mt-0.5">{{ $item->student->student_code ?? '---' }}</div>
        </td>
        <td class="px-6 py-4 text-sm text-slate-600">
            {{ $item->student->studentClass->code ?? '' }}
        </td>
        <td class="px-6 py-4">
            {{-- Lấy thông tin từ quan hệ subject --}}
            <div class="text-sm font-medium text-slate-800 dark:text-white">
                {{ $item->subject->name ?? 'Môn không tồn tại' }}
            </div>
            <div class="text-xs text-slate-500 font-mono">
                {{ $item->subject->code ?? $item->subject_id }} - {{ $item->subject->credits ?? 0 }} TC
            </div>
        </td>
        <td class="px-6 py-4 text-slate-600 text-sm">
            {{ $item->semester->name ?? '' }}
        </td>
        <td class="px-6 py-4 text-center">
            <span class="px-2 py-1 bg-red-50 text-red-600 border border-red-100 rounded text-xs font-bold">
                {{ $item->reason }}
            </span>
        </td>
        <td class="px-6 py-4 text-right">
            <form action="{{ route($routePrefix . 'course_cancellations.destroy', $item->id) }}" method="POST"
                onsubmit="return confirm('Xóa bản ghi này?')">
                @csrf @method('DELETE')
                <button class="p-1.5 hover:bg-red-50 text-slate-400 hover:text-red-600 rounded transition-colors">
                    <span class="material-symbols-outlined !text-[18px]">delete</span>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-12 text-center text-slate-500 italic">Chưa có dữ liệu xóa học phần nào.</td>
    </tr>
@endforelse
