@extends('layouts.admin')
@section('title', 'Import Cảnh Báo Học Tập')

@section('content')
    {{-- Container mở rộng full màn hình --}}
    <div class="w-full px-4 py-4 h-[calc(100vh-80px)] flex flex-col">
        
        @if(!isset($previewData))
            {{-- FORM GIAI ĐOẠN 1: UPLOAD & PREVIEW --}}
            <form action="{{ route('admin.academic_warnings.preview') }}" method="POST" enctype="multipart/form-data" class="flex flex-col h-full">
                @csrf
        @else
            {{-- FORM GIAI ĐOẠN 2: CONFIRM STORE --}}
            <form action="{{ route('admin.academic_warnings.store') }}" method="POST" class="flex flex-col h-full">
                @csrf
                <input type="hidden" name="data" value="{{ json_encode($previewData) }}">
                <input type="hidden" name="semester_id" value="{{ $semester_id }}">
        @endif

        {{-- === PHẦN 1: THANH CÔNG CỤ (TOOLBAR) === --}}
        <div class="bg-white dark:bg-surface-dark border border-slate-200 dark:border-slate-700 p-4 rounded-lg shadow-sm mb-4 flex flex-wrap items-center justify-between gap-4 sticky top-0 z-20">
            
            {{-- Nhóm bên trái: Quay lại & Học kỳ --}}
            <div class="flex items-center gap-4 flex-1">
                <a href="{{ route('admin.academic_warnings.index') }}" 
                   class="flex items-center gap-2 text-slate-500 hover:text-red-600 transition-colors font-medium text-sm">
                    <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
                    Hủy bỏ & Trở về
                </a>

                <div class="h-6 w-px bg-slate-300 dark:bg-slate-700"></div>

                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-slate-400 !text-[20px]">calendar_month</span>
                    <select name="semester_id" class="bg-slate-50 dark:bg-slate-800 border-none text-sm font-semibold text-slate-700 dark:text-slate-200 focus:ring-0 cursor-pointer py-1 pl-2 pr-8 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        @foreach($semesters as $sem)
                            <option value="{{ $sem->id }}" {{ (isset($semester_id) && $semester_id == $sem->id) ? 'selected' : '' }}>
                                {{ $sem->name }} ({{ $sem->academic_year }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Nhóm bên phải: Các nút thao tác --}}
            <div class="flex items-center gap-3">
                @if(!isset($previewData))
                    {{-- Trạng thái 1: Chưa có dữ liệu --}}
                    
                    {{-- Nút chọn file Excel --}}
                    <label for="file-upload" class="cursor-pointer flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 rounded-lg font-medium text-sm transition-colors border border-slate-200 dark:border-slate-600">
                        <span class="material-symbols-outlined !text-[20px] text-green-600">table_view</span>
                        <span id="toolbar-filename">Chọn file Excel</span>
                        <input id="file-upload" name="file" type="file" class="hidden" accept=".xlsx,.xls,.csv" required onchange="updateFileName(this)" />
                    </label>

                    {{-- Nút Xem dữ liệu --}}
                    <button type="submit" class="flex items-center gap-2 bg-primary hover:bg-indigo-700 text-white px-5 py-2 rounded-lg font-medium text-sm transition-colors shadow-sm shadow-indigo-500/30">
                        <span class="material-symbols-outlined !text-[20px]">visibility</span>
                        Xem dữ liệu
                    </button>
                @else
                    {{-- Trạng thái 2: Đã có dữ liệu (Preview) --}}
                    <div class="px-4 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-lg text-sm font-medium border border-blue-100 dark:border-blue-800 flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[18px]">description</span>
                        File: {{ $selected_file_name ?? 'Data.xlsx' }}
                    </div>
                    
                    <a href="{{ route('admin.academic_warnings.import') }}" class="px-4 py-2 text-slate-500 hover:text-slate-700 font-medium text-sm">
                        Chọn lại
                    </a>
                @endif
            </div>
        </div>

        {{-- === PHẦN 2: KHU VỰC NỘI DUNG (DƯỚI) === --}}
        <div class="flex-1 bg-white dark:bg-surface-dark rounded-lg shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden relative flex flex-col">
            
            @if(!isset($previewData))
                {{-- VIEW 1: DROPZONE UPLOAD --}}
                <div class="flex-1 flex flex-col items-center justify-center p-10 border-4 border-dashed border-slate-100 dark:border-slate-800 m-4 rounded-xl bg-slate-50/50 dark:bg-slate-800/30 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors group cursor-pointer" onclick="document.getElementById('file-upload').click()">
                    <div class="bg-white dark:bg-slate-700 p-6 rounded-full shadow-sm mb-4 group-hover:scale-110 transition-transform duration-300">
                        <span class="material-symbols-outlined text-6xl text-slate-300 dark:text-slate-500 group-hover:text-primary transition-colors">cloud_upload</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-700 dark:text-slate-200 mb-2">Tải lên danh sách cảnh báo</h3>
                    <p class="text-slate-500 dark:text-slate-400 mb-6">Kéo thả file vào đây hoặc bấm nút "Chọn file Excel" ở trên</p>
                    <div class="flex gap-4 text-xs text-slate-400 font-mono">
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.XLSX</span>
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.XLS</span>
                        <span class="bg-slate-100 dark:bg-slate-900 px-2 py-1 rounded border dark:border-slate-700">.CSV</span>
                    </div>
                </div>

            @else
                {{-- VIEW 2: BẢNG DỮ LIỆU --}}
                <div class="flex-1 overflow-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800 sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">MSSV</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Họ tên</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Lớp</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Khoa</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">ĐTB</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-center">TC Rớt</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700">Mức CB</th>
                                <th class="p-4 font-semibold text-slate-600 dark:text-slate-400 border-b dark:border-slate-700 text-right">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            @foreach($previewData as $row)
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors {{ !$row['exists'] ? 'bg-red-50/50 dark:bg-red-900/10' : '' }}" id="row-{{ $row['mssv'] }}">
                                    <td class="p-4 font-medium {{ !$row['exists'] ? 'text-red-600' : 'text-primary' }}">{{ $row['mssv'] }}</td>
                                    <td class="p-4 font-medium dark:text-slate-300">{{ $row['fullname'] }}</td>
                                    <td class="p-4 text-slate-500">{{ $row['class_code'] }}</td>
                                    <td class="p-4 text-slate-500">{{ $row['department'] }}</td>
                                    <td class="p-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $row['gpa_term'] }}</td>
                                    <td class="p-4 text-center {{ $row['credits_failed'] > 0 ? 'text-red-500 font-bold' : '' }}">{{ $row['credits_failed'] }}</td>
                                    <td class="p-4">
                                        @if($row['warning_level'] == 1) <span class="badge-yellow">Lần 1</span>
                                        @elseif($row['warning_level'] == 2) <span class="badge-orange">Lần 2</span>
                                        @elseif($row['warning_level'] >= 3) <span class="badge-red">Thôi học</span>
                                        @else <span class="badge-gray">Khác</span> @endif
                                    </td>
                                    <td class="p-4 text-right">
                                        @if($row['exists'])
                                            {{-- Trạng thái: Hợp lệ (Badge) --}}
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/50">
                                                <span class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400">check_circle</span>
                                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Hợp lệ</span>
                                            </div>
                                        @else
                                            {{-- Trạng thái: Chưa có -> Nút Thêm (Style Mới) --}}
                                            <button type="button" 
                                                onclick="openQuickAddModal('{{ $row['mssv'] }}', '{{ $row['fullname'] }}', '{{ $row['dob'] }}', '{{ $row['class_code'] ?? '' }}')" 
                                                class="group inline-flex items-center gap-2 px-3 py-1.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-600 rounded-md shadow-sm hover:border-primary hover:ring-1 hover:ring-primary/20 hover:text-primary transition-all duration-200">
                                                
                                                {{-- Icon --}}
                                                <span class="material-symbols-outlined text-[18px] text-slate-400 group-hover:text-primary transition-colors">
                                                    person_add
                                                </span>
                                                
                                                {{-- Text --}}
                                                <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 group-hover:text-primary">
                                                    Thêm hồ sơ
                                                </span>

                                                {{-- Arrow (Slide in effect) --}}
                                                <span class="material-symbols-outlined text-[14px] text-primary opacity-0 -ml-2 group-hover:opacity-100 group-hover:ml-0 transition-all duration-200">
                                                    arrow_forward
                                                </span>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- NÚT XÁC NHẬN Ở DƯỚI CÙNG (Sticky Bottom) --}}
                <div class="p-4 bg-slate-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <div class="text-sm text-slate-500">
                        Đang xem trước <span class="font-bold text-slate-900 dark:text-white">{{ count($previewData) }}</span> dòng dữ liệu.
                    </div>
                    <button type="submit" class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-8 py-2.5 rounded-lg font-bold shadow-lg shadow-green-600/20 transform hover:-translate-y-0.5 transition-all">
                        <span class="material-symbols-outlined">save</span>
                        Xác nhận & Lưu vào hệ thống
                    </button>
                </div>
            @endif

        </div>

        </form> {{-- Đóng thẻ Form --}}
    </div>

    {{-- === MODAL THÊM SINH VIÊN NHANH === --}}
    <div id="quickAddModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-700">
                    
                    {{-- Header Modal --}}
                    <div class="bg-white dark:bg-[#1e1e2d] px-4 pb-4 pt-5 sm:p-6 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-center gap-4">
                            <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <span class="material-symbols-outlined text-blue-600">person_add</span>
                            </div>
                            <div class="mt-3 text-center sm:ml-0 sm:mt-0 sm:text-left">
                                <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white" id="modal-title">
                                    Thêm sinh viên vào CSDL
                                </h3>
                                <div class="mt-1">
                                    <p class="text-sm text-slate-500">
                                        Sinh viên này chưa có trong hệ thống. Vui lòng kiểm tra và bổ sung thông tin.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form id="quickAddForm">
                        <div class="px-6 py-4 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Mã số SV <span class="text-red-500">*</span></label>
                                    <input type="text" id="qa_mssv" name="mssv" readonly class="w-full bg-slate-100 dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary cursor-not-allowed text-slate-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Ngày sinh</label>
                                    <input type="date" id="qa_dob" name="dob" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Họ và tên <span class="text-red-500">*</span></label>
                                <input type="text" id="qa_fullname" name="fullname" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="qa_email" name="email" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary" required>
                                <p class="text-[10px] text-slate-400 mt-1">Mặc định: MSSV@vnkgu.edu.vn</p>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase mb-1">Lớp sinh hoạt</label>
                                <select id="qa_class_id" name="class_id" class="w-full bg-white dark:bg-slate-800 border-slate-300 dark:border-slate-600 rounded-sm text-sm focus:ring-primary focus:border-primary">
                                    <option value="">-- Chọn lớp (Nếu có) --</option>
                                    @if(isset($classes))
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->code }} - {{ $c->name }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>

                        {{-- Footer Buttons --}}
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-700">
                            <button type="submit" id="btn-qa-save" class="inline-flex w-full justify-center rounded-sm bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-all">
                                <span class="material-symbols-outlined !text-[18px] mr-1">save</span> Lưu vào CSDL
                            </button>
                            <button type="button" onclick="closeQuickAddModal()" class="mt-3 inline-flex w-full justify-center rounded-sm bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 sm:mt-0 sm:w-auto">
                                Hủy bỏ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script>
        // 1. Script Upload File
        function updateFileName(input) {
            const fileNameSpan = document.getElementById('toolbar-filename');
            if (input.files && input.files.length > 0) {
                fileNameSpan.textContent = input.files[0].name;
                fileNameSpan.classList.add('text-primary', 'font-bold');
            } else {
                fileNameSpan.textContent = 'Chọn file Excel';
                fileNameSpan.classList.remove('text-primary', 'font-bold');
            }
        }

        // 2. Script Modal & Ajax
        const modal = document.getElementById('quickAddModal');
        const form = document.getElementById('quickAddForm');
        const btnSave = document.getElementById('btn-qa-save');

        function removeVietnameseTones(str) {
            str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a"); 
            str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e"); 
            str = str.replace(/ì|í|ị|ỉ|ĩ/g,"i"); 
            str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o"); 
            str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u"); 
            str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y"); 
            str = str.replace(/đ/g,"d");
            str = str.replace(/À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ/g, "A");
            str = str.replace(/È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ/g, "E");
            str = str.replace(/Ì|Í|Ị|Ỉ|Ĩ/g, "I");
            str = str.replace(/Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ/g, "O");
            str = str.replace(/Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ/g, "U");
            str = str.replace(/Ỳ|Ý|Ỵ|Ỷ|Ỹ/g, "Y");
            str = str.replace(/Đ/g, "D");
            return str.toLowerCase();
        }
        function openQuickAddModal(mssv, fullname, dobRaw, classCodeRaw) {
            // 1. Điền thông tin cơ bản
            document.getElementById('qa_mssv').value = mssv;
            document.getElementById('qa_fullname').value = fullname;

            if(fullname) {
                const parts = fullname.trim().split(/\s+/); 
                const lastName = parts[parts.length - 1]; 
                const slugName = removeVietnameseTones(lastName);
                
                document.getElementById('qa_email').value = `${slugName}${mssv}@vnkgu.edu.vn`;
            }
            
            const dobInput = document.getElementById('qa_dob');
            if (dobRaw) {
                dobInput.value = dobRaw;
            } else {
                dobInput.value = ''; // Reset nếu không có ngày sinh
            }
            // 2. LOGIC TỰ ĐỘNG CHỌN LỚP (AUTO-SELECT CLASS)
            const classSelect = document.getElementById('qa_class_id');
            
            // Reset về mặc định trước
            classSelect.selectedIndex = 0; 

            if (classCodeRaw && classCodeRaw.trim() !== '') {
                const targetCode = classCodeRaw.trim().toUpperCase(); // Chẩn hóa mã từ Excel (VD: "  b022tt2 ") -> "B022TT2"

                // Duyệt qua tất cả các option trong Select
                for (let i = 0; i < classSelect.options.length; i++) {
                    const optionText = classSelect.options[i].text.toUpperCase(); // Lấy text hiển thị (VD: "B022TT2 - KỸ THUẬT PHẦN MỀM")
                    
                    // Kiểm tra xem Text trong dropdown có CHỨA mã lớp từ Excel không
                    // Ví dụ: "B022TT2 - ..." có chứa "B022TT2" không? -> Có
                    if (optionText.includes(targetCode)) {
                        classSelect.selectedIndex = i; // Chọn option này
                        
                        // Hiệu ứng visual báo hiệu đã tìm thấy (Optional)
                        classSelect.classList.add('border-green-500', 'bg-green-50');
                        setTimeout(() => classSelect.classList.remove('border-green-500', 'bg-green-50'), 2000);
                        
                        break; // Tìm thấy rồi thì dừng vòng lặp
                    }
                }
            }

            // 3. Hiện Modal
            modal.classList.remove('hidden');
        }

        function closeQuickAddModal() {
            modal.classList.add('hidden');
            form.reset();
        }

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const originalText = btnSave.innerHTML;
            btnSave.innerHTML = '<span class="material-symbols-outlined animate-spin !text-[18px] mr-1">sync</span> Đang lưu...';
            btnSave.disabled = true;

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch('{{ route("admin.academic_warnings.quick_add_student") }}', {
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
                    closeQuickAddModal();
                    
                    // Cập nhật dòng UI thành Hợp lệ
                    const row = document.getElementById('row-' + data.mssv);
                    if(row) {
                        row.classList.remove('bg-red-50/50', 'dark:bg-red-900/10');
                        row.classList.add('bg-green-50/30');
                        row.lastElementChild.innerHTML = `
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-900/50 animate-pulse">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600 dark:text-emerald-400">check_circle</span>
                                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">Đã thêm</span>
                            </div>`;
                        row.firstElementChild.classList.remove('text-red-600');
                        row.firstElementChild.classList.add('text-primary');
                    }
                } else {
                    alert('Lỗi: ' + result.message);
                }
            })
            .catch(error => { console.error(error); alert('Lỗi kết nối server'); })
            .finally(() => {
                btnSave.innerHTML = originalText;
                btnSave.disabled = false;
            });
        });
    </script>

    {{-- STYLE --}}
    <style>
        .badge-yellow { @apply px-2 py-0.5 rounded text-xs font-semibold bg-yellow-100 text-yellow-700 border border-yellow-200; }
        .badge-orange { @apply px-2 py-0.5 rounded text-xs font-semibold bg-orange-100 text-orange-700 border border-orange-200; }
        .badge-red { @apply px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 border border-red-200; }
        .badge-gray { @apply px-2 py-0.5 rounded text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200; }
    </style>
@endsection