@if (count($previewData) > 0)
    <div class="mt-4 border rounded-sm overflow-hidden">
        <div class="bg-slate-100 px-4 py-2 border-b font-bold text-sm text-slate-700 flex justify-between items-center">
            <span>Dữ liệu đọc được: {{ count($previewData) }} sinh viên</span>

            {{-- Chú thích --}}
            <div class="flex gap-3 text-xs font-normal">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-emerald-500"></span> Hợp
                    lệ</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-500"></span> Trùng mã (Sẽ
                    lỗi)</span>
            </div>
        </div>

        <div class="overflow-x-auto max-h-60 overflow-y-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-500 font-semibold text-xs uppercase sticky top-0 shadow-sm">
                    <tr>
                        <th class="px-4 py-2">Mã SV</th>
                        <th class="px-4 py-2">Họ và Tên</th>
                        <th class="px-4 py-2">Ngày sinh</th>
                        <th class="px-4 py-2">Trạng thái</th>
                        <th class="px-4 py-2 text-right">Kết quả</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($previewData as $row)
                        <tr class="{{ $row['is_duplicate'] ? 'bg-red-50' : 'hover:bg-slate-50' }}">
                            <td
                                class="px-4 py-2 font-mono {{ $row['is_duplicate'] ? 'text-red-600 font-bold' : 'text-primary' }}">
                                {{ $row['mssv'] }}
                            </td>
                            <td class="px-4 py-2">{{ $row['name'] }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $row['dob'] }}</td>
                            <td class="px-4 py-2 text-slate-500">{{ $row['status'] }}</td>
                            <td class="px-4 py-2 text-right">
                                @if ($row['is_duplicate'])
                                    <span class="text-red-600 font-bold text-xs flex items-center justify-end gap-1">
                                        <span class="material-symbols-outlined !text-[16px]">error</span> Đã tồn tại
                                    </span>
                                @else
                                    <span
                                        class="text-emerald-600 font-bold text-xs flex items-center justify-end gap-1">
                                        <span class="material-symbols-outlined !text-[16px]">check_circle</span> OK
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
