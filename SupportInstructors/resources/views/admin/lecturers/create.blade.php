@extends('layouts.admin')
@section('title', 'Thêm Giảng viên')

@section('content')
    <div class="w-full px-4 py-6">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.lecturers.index') }}"
                    class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 transition-colors shadow-sm">
                    <span class="material-symbols-outlined !text-[18px] block">arrow_back</span>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Thêm Giảng viên</h1>
                    <p class="text-xs text-slate-500">Tạo tài khoản mới cho Giảng viên/Cố vấn học tập</p>
                </div>
            </div>
        </div>

        {{-- Hiển thị lỗi chung (Ngoại lệ Database) --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Lỗi hệ thống!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Hiển thị thông báo nếu có lỗi Validate (Tùy chọn, để User dễ nhìn) --}}
        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Vui lòng kiểm tra lại dữ liệu nhập vào!</p>
                <ul class="list-disc pl-5 text-sm mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.lecturers.store') }}" method="POST" enctype="multipart/form-data" novalidate>
            @csrf
            <div class="flex flex-col lg:flex-row gap-6">

                {{-- SIDEBAR TRÁI: AVATAR --}}
                <div class="w-full lg:w-1/3 xl:w-1/4 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-6 shadow-sm flex flex-col items-center">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-6 w-full border-b pb-2">Ảnh đại diện</h4>

                        <div class="relative group w-40 h-40 mb-4">
                            <img id="avatar-preview"
                                src="https://ui-avatars.com/api/?name=Lecturer&background=f1f5f9&color=64748b&size=256"
                                alt="Avatar Preview"
                                class="w-full h-full rounded-full object-cover border-4 border-slate-50 shadow-md group-hover:border-primary/20 transition-all">

                            <label for="avatar-input"
                                class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer">
                                <span class="material-symbols-outlined text-white !text-3xl">photo_camera</span>
                            </label>

                            <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden"
                                onchange="previewImage(this)">
                        </div>

                        <p class="text-[11px] text-slate-400 text-center italic">Định dạng JPG, PNG. Tối đa 2MB</p>
                        @error('avatar')
                            <span class="text-red-500 text-xs font-medium mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- NỘI DUNG PHẢI: THÔNG TIN CHI TIẾT --}}
                <div class="flex-1 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                            <h3 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary !text-[18px]">assignment_ind</span>
                                Thông tin tài khoản & Công tác
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                                {{-- Họ tên --}}
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Họ và Tên <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="lecturer_name" value="{{ old('name') }}"
                                        required placeholder="Nhập tên Giảng viên (Ví dụ: Võ Hoàng Nhân)"
                                        class="w-full px-3 py-2.5 border rounded-sm text-sm focus:ring-1 focus:outline-none transition-colors
                                        {{ $errors->has('name') ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50' : 'border-slate-300 focus:border-primary focus:ring-primary' }}">
                                    @error('name')
                                        <p class="text-red-500 text-xs font-medium mt-1 flex items-center gap-1">
                                            <span class="material-symbols-outlined !text-[14px]">error</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Email Hệ
                                        Thống</label>
                                    <div class="relative">
                                        {{-- KHÔNG dùng readonly nữa, cho phép sửa --}}
                                        <input type="email" name="email" id="lecturer_email" value="{{ old('email') }}"
                                            placeholder="Tự động tạo hoặc nhập thủ công"
                                            class="w-full px-3 py-2.5 border rounded-sm text-sm focus:ring-1 focus:outline-none transition-colors pr-10
                                            {{ $errors->has('email') ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50' : 'border-slate-300 focus:border-primary focus:ring-primary' }}">
                                        <span
                                            class="absolute right-3 top-2.5 text-slate-400 material-symbols-outlined !text-[18px]">mail</span>
                                    </div>
                                    @error('email')
                                        <p class="text-red-500 text-xs font-medium mt-1 flex items-center gap-1">
                                            <span
                                                class="material-symbols-outlined !text-[14px]">error</span>{{ $message }}
                                        </p>
                                    @else
                                        <p class="text-[11px] text-blue-500 mt-1 italic">Email sẽ tự động tạo từ tên (vd:
                                            vhnhan@vnkgu.edu.vn)</p>
                                    @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Số điện
                                        thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}"
                                        placeholder="Nhập số điện thoại liên lạc"
                                        class="w-full px-3 py-2.5 border rounded-sm text-sm focus:ring-1 focus:outline-none transition-colors border-slate-300 focus:border-primary focus:ring-primary">
                                </div>

                                <div class="md:col-span-2 border-t border-slate-100 my-2"></div>

                                {{-- Mã Giảng viên --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Mã Giảng viên
                                        <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input type="text" name="lecturer_code" value="{{ old('lecturer_code') }}"
                                            required placeholder="VD: GV001"
                                            class="w-full px-3 py-2.5 border rounded-sm font-mono text-sm uppercase focus:ring-1 focus:outline-none transition-colors pr-10
                                            {{ $errors->has('lecturer_code') ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50' : 'border-slate-300 focus:border-primary focus:ring-primary' }}">
                                        <span
                                            class="absolute right-3 top-2.5 text-slate-400 material-symbols-outlined !text-[18px]">badge</span>
                                    </div>
                                    @error('lecturer_code')
                                        <p class="text-red-500 text-xs font-medium mt-1 flex items-center gap-1">
                                            <span
                                                class="material-symbols-outlined !text-[14px]">error</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Khoa --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Khoa / Đơn vị
                                        <span class="text-red-500">*</span></label>
                                    <select name="department_id" required
                                        class="w-full px-3 py-2.5 border rounded-sm text-sm cursor-pointer focus:ring-1 focus:outline-none transition-colors
                                        {{ $errors->has('department_id') ? 'border-red-500 focus:border-red-500 focus:ring-red-500 bg-red-50' : 'border-slate-300 focus:border-primary focus:ring-primary' }}">
                                        <option value="">-- Chọn Khoa --</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <p class="text-red-500 text-xs font-medium mt-1 flex items-center gap-1">
                                            <span
                                                class="material-symbols-outlined !text-[14px]">error</span>{{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Học vị --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Học vị</label>
                                    <select name="degree"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm cursor-pointer focus:border-primary">
                                        <option value="Cử nhân" {{ old('degree') == 'Cử nhân' ? 'selected' : '' }}>Cử nhân
                                        </option>
                                        <option value="Thạc sĩ" {{ old('degree') == 'Thạc sĩ' ? 'selected' : '' }}>Thạc sĩ
                                        </option>
                                        <option value="Tiến sĩ" {{ old('degree') == 'Tiến sĩ' ? 'selected' : '' }}>Tiến sĩ
                                        </option>
                                        <option value="PGS.TS" {{ old('degree') == 'PGS.TS' ? 'selected' : '' }}>PGS.TS
                                        </option>
                                        <option value="GS.TS" {{ old('degree') == 'GS.TS' ? 'selected' : '' }}>GS.TS
                                        </option>
                                    </select>
                                </div>

                                {{-- Chức vụ --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Chức vụ</label>
                                    <input type="text" name="position" value="{{ old('position', 'Giảng viên') }}"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:border-primary focus:ring-primary text-sm">
                                </div>
                            </div>

                            <div class="mt-10 flex items-center justify-end gap-3">
                                <a href="{{ route('admin.lecturers.index') }}"
                                    class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 transition-colors text-sm">Hủy
                                    bỏ</a>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 shadow-sm flex items-center gap-2 text-sm transition-all active:scale-95">
                                    <span class="material-symbols-outlined !text-[18px]">save</span> Lưu Giảng viên
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // 1. Preview Ảnh
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // 2. Tự động tạo Email từ Tên (Chỉ khi ô Email đang trống)
        const nameInput = document.getElementById('lecturer_name');
        const emailInput = document.getElementById('lecturer_email');

        nameInput.addEventListener('input', function() {
            // Nếu người dùng đã gõ email bằng tay và bị báo lỗi, thì không auto-gen đè lên
            // Trừ khi họ xóa sạch ô email

            const fullName = this.value;
            if (!fullName) {
                // Không xóa email nếu form đang có lỗi email từ server trả về
                if (!emailInput.classList.contains('border-red-500')) {
                    emailInput.value = '';
                }
                return;
            }

            // Chuyển sang tiếng Việt không dấu
            let str = fullName.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            str = str.replace(/đ/g, "d").replace(/Đ/g, "D");
            str = str.trim().toLowerCase();

            // Tách từ
            const parts = str.split(/\s+/);
            if (parts.length < 1) return;

            const lastName = parts.pop(); // Lấy tên (ví dụ: nhan)
            let initials = "";
            parts.forEach(part => {
                if (part.length > 0) initials += part.charAt(0); // Lấy chữ đầu họ và đệm (ví dụ: v, h)
            });

            // Chỉ auto-fill nếu ô email đang trống HOẶC không có class báo lỗi đỏ
            if (!emailInput.classList.contains('border-red-500') || emailInput.value === '') {
                if (lastName) {
                    const generatedEmail = initials + lastName + "@vnkgu.edu.vn";
                    emailInput.value = generatedEmail;
                }
            }
        });
    </script>
@endsection
