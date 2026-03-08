@extends('layouts.admin')
@section('title', 'Xem Trước Dữ Liệu')

@section('content')
<div class="flex flex-col h-[calc(100vh-100px)]">
    {{-- HEADER: TIÊU ĐỀ & NÚT CONFIRM --}}
    <div class="flex justify-between items-center mb-4">
        <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white">Kiểm tra dữ liệu Xóa học phần</h1>
            <p class="text-sm text-slate-500">Vui lòng rà soát dữ liệu. Hệ thống chỉ lưu các dòng hợp lệ (Màu trắng).</p>
        </div>
        <form action="{{ route('admin.course_cancellations.store_import') }}" method="POST">
            @csrf
            <input type="hidden" name="semester_id" value="{{ $semester_id }}">
            <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
            
            <div class="flex gap-2">
                <a href="{{ route('admin.course_cancellations.import') }}" class="px-4 py-2 border border-slate-300 rounded-lg text-slate-600 hover:bg-slate-50 text-sm font-medium transition-colors">
                    Quay lại
                </a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-indigo-700 text-sm font-medium shadow-md transition-colors flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[18px]">save</span>
                    Xác nhận Import
                </button>
            </div>
        </form>
    </div>

    {{-- BODY: BẢNG DỮ LIỆU (SCROLLABLE) --}}
    <div class="flex-1 bg-white dark:bg-[#1e1e2d] rounded-xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col">
        <div class="overflow-auto flex-1">
            <table class="w-full text-left border-collapse text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                    <tr>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Lớp</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">MSSV</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Họ tên</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Mã Môn</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Tên Môn</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">TC</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">Lý do</th>
                        <th class="p-3 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-right">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($previewData as $row)
                        {{-- Logic màu nền: Đỏ nếu lỗi (SV hoặc Môn), Trắng nếu hợp lệ --}}
                        <tr class="transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/50 {{ (!$row['student_exists'] || !$row['subject_exists']) ? 'bg-red-50 dark:bg-red-900/10' : '' }}" 
                            id="row-{{ $row['subject_code'] }}-{{ $row['student_code'] }}">
                            
                            {{-- Lớp --}}
                            <td class="p-3 text-slate-500 font-medium">{{ $row['class_code'] }}</td>

                            {{-- MSSV --}}
                            <td class="p-3 font-medium font-mono {{ !$row['student_exists'] ? 'text-red-600 font-bold' : 'text-primary' }}">
                                {{ $row['student_code'] }}
                            </td>

                            {{-- Họ tên --}}
                            <td class="p-3 font-medium dark:text-slate-300">
                                {{ $row['student_name'] }}
                            </td>

                            {{-- Mã Môn (Class để JS tìm và update hàng loạt) --}}
                            <td class="p-3 font-mono subject-code-cell {{ !$row['subject_exists'] ? 'text-red-600 font-bold' : 'text-slate-600 dark:text-slate-400' }}" 
                                data-code="{{ $row['subject_code'] }}">
                                {{ $row['subject_code'] }}
                            </td>

                            {{-- Tên Môn --}}
                            <td class="p-3 text-slate-600 dark:text-slate-400 max-w-[200px] truncate" title="{{ $row['subject_name'] }}">
                                {{ $row['subject_name'] }}
                            </td>

                            {{-- Tín chỉ --}}
                            <td class="p-3 text-center">{{ $row['credits'] }}</td>

                            {{-- Lý do --}}
                            <td class="p-3 text-center">
                                <span class="px-2 py-0.5 rounded text-xs bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600">
                                    {{ $row['reason'] }}
                                </span>
                            </td>

                            {{-- Trạng thái & Action --}}
                            <td class="p-3 text-right action-cell">
                                @if($row['student_exists'] && $row['subject_exists'])
                                    <span class="inline-flex items-center justify-end gap-1 text-green-600 text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm">check_circle</span>
                                        Hợp lệ
                                    </span>
                                @else
                                    <div class="flex flex-col items-end gap-1">
                                        
                                        {{-- Lỗi 1: Sinh viên chưa có (Chỉ báo lỗi, không cho thêm nhanh theo yêu cầu) --}}
                                        @if(!$row['student_exists'])
                                            <span class="px-2 py-0.5 rounded text-xs bg-red-100 text-red-700 border border-red-200 font-bold">
                                                Chưa có SV
                                            </span>
                                        @endif

                                        {{-- Lỗi 2: Môn học chưa có (Hiện nút thêm nhanh) --}}
                                        @if(!$row['subject_exists'])
                                            <button type="button"
                                                onclick="openQuickAddSubject('{{ $row['subject_code'] }}', '{{ $row['subject_name'] }}', '{{ $row['credits'] }}')" 
                                                class="btn-add-subject inline-flex items-center gap-1 bg-white dark:bg-slate-700 border border-orange-300 dark:border-orange-600 hover:bg-orange-50 dark:hover:bg-orange-900/30 text-orange-600 dark:text-orange-400 px-2 py-1 rounded text-xs font-bold transition-all shadow-sm group">
                                                <span class="material-symbols-outlined text-sm group-hover:scale-110 transition-transform">library_add</span>
                                                Thêm Môn
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        {{-- FOOTER INFO --}}
        <div class="p-3 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center text-xs text-slate-500">
            <span>Hiển thị <span class="font-bold text-slate-800 dark:text-white">{{ count($previewData) }}</span> dòng dữ liệu.</span>
            <span>Các dòng có màu đỏ (lỗi) sẽ bị bỏ qua khi lưu.</span>
        </div>
    </div>
</div>

{{-- === MODAL THÊM MÔN HỌC (Ẩn mặc định) === --}}
<div id="quickAddSubjectModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                
                {{-- Header Modal --}}
                <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-4">
                        <div class="mx-auto flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-orange-100 sm:mx-0">
                            <span class="material-symbols-outlined text-orange-600">library_add</span>
                        </div>
                        <div class="mt-3 text-center sm:ml-0 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white">Thêm Môn Học Mới</h3>
                            <p class="text-sm text-slate-500 mt-1">Môn này chưa có trong CSDL. Thêm ngay để import hợp lệ.</p>
                        </div>
                    </div>
                </div>

                {{-- Form Body --}}
                <form id="quickAddSubjectForm">
                    <div class="px-6 py-4 space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Mã Học Phần</label>
                            <input type="text" id="qas_code" name="code" readonly class="w-full bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm font-mono text-slate-500 cursor-not-allowed font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Tên Môn Học <span class="text-red-500">*</span></label>
                            <input type="text" id="qas_name" name="name" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Số Tín Chỉ <span class="text-red-500">*</span></label>
                            <input type="number" id="qas_credits" name="credits" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary" required min="0">
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-700">
                        <button type="submit" id="btn-qas-save" class="inline-flex w-full justify-center rounded-sm bg-orange-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 sm:ml-3 sm:w-auto transition-all">
                            <span class="material-symbols-outlined !text-[18px] mr-1">save</span> Lưu Môn Học
                        </button>
                        <button type="button" onclick="closeQuickAddSubjectModal()" class="mt-3 inline-flex w-full justify-center rounded-sm bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 sm:mt-0 sm:w-auto">
                            Hủy bỏ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --- SCRIPT XỬ LÝ QUICK ADD SUBJECT ---
    const subjectModal = document.getElementById('quickAddSubjectModal');
    const subjectForm = document.getElementById('quickAddSubjectForm');
    const btnSubjectSave = document.getElementById('btn-qas-save');

    // 1. Mở Modal
    function openQuickAddSubject(code, name, credits) {
        document.getElementById('qas_code').value = code;
        document.getElementById('qas_name').value = name;
        document.getElementById('qas_credits').value = credits;
        subjectModal.classList.remove('hidden');
    }

    // 2. Đóng Modal
    function closeQuickAddSubjectModal() {
        subjectModal.classList.add('hidden');
    }

    // 3. Xử lý Submit Form (Ajax)
    subjectForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const originalText = btnSubjectSave.innerHTML;
        btnSubjectSave.innerHTML = '<span class="material-symbols-outlined animate-spin !text-[18px] mr-1">sync</span> Đang lưu...';
        btnSubjectSave.disabled = true;

        const formData = new FormData(subjectForm);
        const data = Object.fromEntries(formData.entries());

        // Gọi API Thêm môn học
        fetch('{{ route("admin.subjects.quick_store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                closeQuickAddSubjectModal();
                
                // --- CẬP NHẬT GIAO DIỆN HÀNG LOẠT ---
                const targetCode = data.code;
                
                // Tìm tất cả các ô chứa mã môn này trong bảng
                const codeCells = document.querySelectorAll(`.subject-code-cell[data-code="${targetCode}"]`);
                
                codeCells.forEach(cell => {
                    // Đổi màu text mã môn thành bình thường
                    cell.classList.remove('text-red-600', 'font-bold');
                    cell.classList.add('text-slate-600', 'dark:text-slate-400');
                    
                    // Tìm dòng chứa ô này
                    const row = cell.closest('tr');
                    
                    // Xóa nút "Thêm Môn"
                    const actionCell = row.querySelector('.action-cell');
                    const addBtn = actionCell.querySelector('.btn-add-subject');
                    if(addBtn) addBtn.remove();

                    // Logic: Nếu không còn lỗi Sinh viên (badge đỏ) -> Dòng chuyển thành hợp lệ (xanh)
                    const hasStudentError = row.querySelector('.bg-red-100'); // Class của badge lỗi SV
                    
                    if (!hasStudentError) {
                        // Đổi màu nền dòng
                        row.classList.remove('bg-red-50', 'dark:bg-red-900/10');
                        row.classList.add('bg-white', 'dark:bg-[#1e1e2d]');
                        
                        // Thêm badge Hợp lệ
                        actionCell.innerHTML = `
                            <span class="inline-flex items-center justify-end gap-1 text-green-600 text-xs font-bold animate-pulse">
                                <span class="material-symbols-outlined text-sm">check_circle</span>
                                Hợp lệ
                            </span>`;
                    }
                });

                // Thông báo nhỏ
                // alert('Đã thêm môn ' + targetCode + ' thành công!');

            } else {
                alert('Lỗi: ' + result.message);
            }
        })
        .catch(error => { 
            console.error(error); 
            alert('Lỗi kết nối server'); 
        })
        .finally(() => {
            btnSubjectSave.innerHTML = originalText;
            btnSubjectSave.disabled = false;
        });
    });
</script>
@endsection