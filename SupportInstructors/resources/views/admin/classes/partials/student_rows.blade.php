@forelse ($students as $student)
    @php
        $isTrashed = $student->trashed();
    @endphp
    <tr
        class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group {{ $isTrashed ? 'bg-slate-100/70 dark:bg-slate-900/50' : '' }}">
        <td class="px-6 py-3 text-center">
            {{-- data-trashed dùng cho JS ở trang show --}}
            <input type="checkbox" value="{{ $student->id }}" data-trashed="{{ $isTrashed ? 'true' : 'false' }}"
                class="student-checkbox select-item rounded border-gray-300 text-primary cursor-pointer">
        </td>
        <td
            class="px-6 py-3 font-mono {{ $isTrashed ? 'text-slate-400 decoration-slate-400' : 'text-slate-700 dark:text-slate-300 font-bold' }}">
            {{ $student->student_code }}
        </td>
        <td class="px-6 py-3 font-medium text-slate-800 dark:text-white">
            <div class="flex items-center gap-2">
                <span class="{{ $isTrashed ? 'text-slate-500 line-through' : '' }}">{{ $student->fullname }}</span>
            </div>
        </td>
        <td class="px-6 py-3 text-slate-600 dark:text-slate-400">
            {{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '--' }}
        </td>
        <td class="px-6 py-3 text-slate-500 text-xs">{{ $student->user->email ?? 'Chưa có' }}</td>

        {{-- SỬA Ở ĐÂY: Thêm 'whitespace-nowrap' để không bị xuống dòng --}}
        <td class="px-6 py-3 whitespace-nowrap">
            @if ($isTrashed)
                <span class="text-xs text-slate-400 italic">Vô hiệu hóa</span>
            @else
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
                <span class="px-2 py-1 rounded text-[10px] font-bold border {{ $statusClass }}">
                    {{ $statusText }}
                </span>
            @endif
        </td>
        <td class="px-6 py-3 text-right">
            <div class="flex justify-end gap-1">
                @if ($isTrashed)
                    {{-- NÚT KHÔI PHỤC --}}
                    <form action="{{ route('admin.students.restore', $student->id) }}" method="POST"
                        class="inline-block">
                        @csrf
                        <button type="button"
                            class="btn-restore-student p-1.5 text-blue-600 hover:bg-blue-100 rounded transition-all"
                            data-url="{{ route('admin.students.restore', $student->id) }}" title="Khôi phục hoạt động">
                            <span class="material-symbols-outlined !text-[18px]">restore</span>
                        </button>
                    </form>
                @else
                    {{-- CÁC NÚT KHI ĐANG HOẠT ĐỘNG --}}
                    <button type="button"
                        class="btn-edit-student p-1.5 text-slate-600 hover:bg-slate-100 rounded transition-all"
                        data-id="{{ $student->id }}" data-code="{{ $student->student_code }}"
                        data-fullname="{{ $student->fullname }}" data-email="{{ $student->user->email ?? '' }}"
                        data-dob="{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('Y-m-d') : '' }}"
                        data-status="{{ $student->status }}" title="Chỉnh sửa">
                        <span class="material-symbols-outlined !text-[18px]">edit</span>
                    </button>

                    <button type="button"
                        class="btn-send-single-email p-1.5 text-blue-600 hover:bg-blue-50 rounded transition-all"
                        data-id="{{ $student->id }}" title="Gửi mail">
                        <span class="material-symbols-outlined !text-[18px]">send</span>
                    </button>

                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST"
                        class="inline-block form-delete-student">
                        @csrf @method('DELETE')
                        <button type="button"
                            class="btn-delete-student p-1.5 text-red-600 hover:bg-red-50 rounded transition-all"
                            data-code="{{ $student->student_code }}" title="Ẩn sinh viên">
                            <span class="material-symbols-outlined !text-[18px]">visibility_off</span>
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="7" class="px-6 py-8 text-center text-slate-500">Không tìm thấy sinh viên nào.</td>
    </tr>
@endforelse
