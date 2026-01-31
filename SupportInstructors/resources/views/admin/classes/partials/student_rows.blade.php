{{-- resources/views/admin/classes/partials/student_rows.blade.php --}}
@forelse ($students as $student)
    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
        <td class="px-6 py-3 text-center">
            <input type="checkbox" name="student_ids[]" value="{{ $student->id }}"
                class="student-checkbox rounded border-gray-300 text-primary cursor-pointer">
        </td>
        <td class="px-6 py-3 font-mono text-slate-700 dark:text-slate-300 font-bold">
            {{ $student->student_code }}
        </td>
        <td class="px-6 py-3 font-medium text-slate-800 dark:text-white">
            {{ $student->fullname }}
        </td>
        {{-- [MỚI] Cột Ngày sinh --}}
        <td class="px-6 py-3 text-slate-600 dark:text-slate-400">
            {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '--' }}
        </td>
        <td class="px-6 py-3 text-slate-500">{{ $student->user->email ?? 'Chưa có' }}</td>
        <td class="px-6 py-3">
            @php
                $statusClass = match ($student->status) {
                    'studying' => 'bg-green-100 text-green-700 border-green-200',
                    'reserved' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'dropped' => 'bg-red-100 text-red-700 border-red-200',
                    'graduated' => 'bg-blue-100 text-blue-700 border-blue-200',
                    default => 'bg-gray-100 text-gray-700 border-gray-200',
                };
                $statusText = match ($student->status) {
                    'studying' => 'Đang học',
                    'reserved' => 'Bảo lưu',
                    'dropped' => 'Thôi học',
                    'graduated' => 'Tốt nghiệp',
                    default => $student->status,
                };
            @endphp
            <span class="px-2 py-1 rounded text-xs font-bold border {{ $statusClass }}">
                {{ $statusText }}
            </span>
        </td>
        <td class="px-6 py-3 text-right flex justify-end gap-1">
            {{-- Nút Sửa (Mở Modal) --}}
            <button type="button"
                class="btn-edit-student p-1.5 text-slate-600 hover:bg-slate-100 rounded border border-transparent hover:border-slate-300 transition-all"
                title="Chỉnh sửa" data-id="{{ $student->id }}" data-fullname="{{ $student->fullname }}"
                data-email="{{ $student->user->email ?? '' }}" data-dob="{{ $student->dob }}"
                data-status="{{ $student->status }}">
                <span class="material-symbols-outlined !text-[18px]">edit</span>
            </button>

            {{-- Nút Gửi Mail Riêng --}}
            <button type="button"
                class="btn-send-single-email p-1.5 text-blue-600 hover:bg-blue-50 rounded border border-transparent hover:border-blue-200 transition-all"
                data-id="{{ $student->id }}" title="Gửi mail">
                <span class="material-symbols-outlined !text-[18px]">send</span>
            </button>

            {{-- Nút Xóa --}}
            <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                class="inline-block form-delete-student">
                @csrf
                @method('DELETE')
                <button type="button"
                    class="btn-delete-student p-1.5 text-red-600 hover:bg-red-50 rounded border border-transparent hover:border-red-200 transition-all"
                    title="Xóa sinh viên" data-code="{{ $student->student_code }}">
                    <span class="material-symbols-outlined !text-[18px]">delete</span>
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-8 text-center text-slate-500">
            Không tìm thấy sinh viên nào.
        </td>
    </tr>
@endforelse
