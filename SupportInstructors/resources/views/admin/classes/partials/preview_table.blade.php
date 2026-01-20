@if (count($previewData) > 0)
    <div class="mt-4 border rounded-sm overflow-hidden border-slate-200 dark:border-slate-700">
        <div
            class="bg-slate-100 dark:bg-slate-800 px-4 py-2 border-b border-slate-200 dark:border-slate-700 font-bold text-sm text-slate-700 dark:text-slate-200 flex justify-between items-center">
            <span>Dữ liệu đọc được: {{ count($previewData) }} sinh viên</span>

            {{-- Chú thích --}}
            <div class="flex gap-3 text-xs font-normal">
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hợp lệ
                </span>
                <span class="flex items-center gap-1">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span> Trùng mã (Sẽ lỗi)
                </span>
            </div>
        </div>

        <div class="overflow-x-auto max-h-60 overflow-y-auto">
            <table class="w-full text-sm text-left">
                <thead
                    class="bg-slate-50 dark:bg-slate-700 text-slate-500 dark:text-slate-300 font-semibold text-xs uppercase sticky top-0 shadow-sm z-10">
                    <tr>
                        <th class="px-4 py-2 bg-slate-50 dark:bg-slate-700">Mã SV</th>
                        <th class="px-4 py-2 bg-slate-50 dark:bg-slate-700">Họ và Tên</th>
                        <th class="px-4 py-2 bg-slate-50 dark:bg-slate-700">Ngày sinh</th>
                        <th class="px-4 py-2 bg-slate-50 dark:bg-slate-700">Trạng thái</th>
                        <th class="px-4 py-2 bg-slate-50 dark:bg-slate-700 text-right">Kết quả</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($previewData as $row)
                        <tr
                            class="{{ $row['is_duplicate'] ? 'bg-red-50 dark:bg-red-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                            <td
                                class="px-4 py-2 font-mono {{ $row['is_duplicate'] ? 'text-red-600 dark:text-red-400 font-bold' : 'text-primary' }}">
                                {{ $row['mssv'] }}
                            </td>
                            <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $row['name'] }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $row['dob'] }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $row['status'] }}</td>
                            <td class="px-4 py-2 text-right">
                                @if ($row['is_duplicate'])
                                    <span
                                        class="text-red-600 dark:text-red-400 font-bold text-xs flex items-center justify-end gap-1">
                                        {{-- Icon 16px -> 14px --}}
                                        <span class="material-symbols-outlined !text-[14px]">error</span> Đã tồn tại
                                    </span>
                                @else
                                    <span
                                        class="text-emerald-600 font-bold text-xs flex items-center justify-end gap-1">
                                        {{-- Icon 16px -> 14px --}}
                                        <span class="material-symbols-outlined !text-[14px]">check_circle</span> OK
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <p class="mt-4 text-center text-slate-400 text-sm italic">Không đọc được dữ liệu nào từ file.</p>
@endif
