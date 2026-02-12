@extends('layouts.admin')
@section('title', 'Cập nhật Giảng viên')

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
                    <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Cập nhật Giảng viên</h1>
                    <p class="text-xs text-slate-500">Chỉnh sửa thông tin thầy/cô: <span
                            class="font-bold text-primary">{{ $lecturer->user->name }}</span></p>
                </div>
            </div>
        </div>

        {{-- Hiển thị lỗi chung --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Lỗi!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('admin.lecturers.update', $lecturer->id) }}" method="POST" enctype="multipart/form-data"
            novalidate>
            @csrf
            @method('PUT')

            <div class="flex flex-col lg:flex-row gap-6">

                {{-- SIDEBAR TRÁI: AVATAR --}}
                <div class="w-full lg:w-1/3 xl:w-1/4 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm p-6 shadow-sm flex flex-col items-center">
                        <h4 class="text-xs font-bold text-slate-400 uppercase mb-6 w-full border-b pb-2">Ảnh đại diện</h4>

                        <div class="relative group w-40 h-40 mb-4">
                            {{-- Logic hiển thị ảnh: Nếu có ảnh trong DB thì lấy, không thì dùng UI Avatars --}}
                            <img id="avatar-preview"
                                src="{{ $lecturer->user->avatar_url ? asset('storage/' . $lecturer->user->avatar_url) : 'https://ui-avatars.com/api/?name=' . urlencode($lecturer->user->name) . '&background=f1f5f9&color=64748b&size=256' }}"
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
                            <span class="text-red-500 text-[11px] mt-2">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                {{-- NỘI DUNG PHẢI: THÔNG TIN CHI TIẾT --}}
                <div class="flex-1 space-y-6">
                    <div
                        class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
                        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                            <h3 class="font-bold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                                <span class="material-symbols-outlined text-primary !text-[18px]">edit_square</span> Thông
                                tin tài khoản & Công tác
                            </h3>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                                {{-- Họ tên --}}
                                <div class="md:col-span-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Họ và Tên <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" id="lecturer_name"
                                        value="{{ old('name', $lecturer->user->name) }}" required
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('name') border-red-500 @enderror">
                                    @error('name')
                                        <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Email --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Email Hệ Thống
                                        <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        {{-- Cho phép sửa email, không readonly như create --}}
                                        <input type="email" name="email"
                                            value="{{ old('email', $lecturer->user->email) }}" required
                                            class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm @error('email') border-red-500 @enderror">
                                        <span
                                            class="absolute right-3 top-2.5 text-slate-400 material-symbols-outlined !text-[16px]">mail</span>
                                    </div>
                                    @error('email')
                                        <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Số điện thoại --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Số điện
                                        thoại</label>
                                    <input type="text" name="phone" value="{{ old('phone', $lecturer->user->phone) }}"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm">
                                </div>

                                <div class="md:col-span-2 border-t border-slate-100 my-2"></div>

                                {{-- Mã Giảng viên --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Mã Giảng viên
                                        <span class="text-red-500">*</span></label>
                                    <input type="text" name="lecturer_code"
                                        value="{{ old('lecturer_code', $lecturer->lecturer_code) }}" required
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary font-mono text-sm uppercase @error('lecturer_code') border-red-500 @enderror">
                                    @error('lecturer_code')
                                        <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Khoa --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Khoa / Đơn vị
                                        <span class="text-red-500">*</span></label>
                                    <select name="department_id" required
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm cursor-pointer">
                                        <option value="">-- Chọn Khoa --</option>
                                        @foreach ($departments as $dept)
                                            <option value="{{ $dept->id }}"
                                                {{ old('department_id', $lecturer->department_id) == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id')
                                        <span class="text-red-500 text-[11px] mt-1">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Học vị --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Học vị</label>
                                    <select name="degree"
                                        class="w-full px-3 py-2 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm cursor-pointer">
                                        @foreach (['Cử nhân', 'Thạc sĩ', 'Tiến sĩ', 'PGS.TS', 'GS.TS'] as $deg)
                                            <option value="{{ $deg }}"
                                                {{ old('degree', $lecturer->degree) == $deg ? 'selected' : '' }}>
                                                {{ $deg }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Chức vụ --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 uppercase">Chức vụ</label>
                                    <input type="text" name="position"
                                        value="{{ old('position', $lecturer->position) }}"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm">
                                </div>
                            </div>

                            <div class="mt-10 flex items-center justify-end gap-3">
                                <a href="{{ route('admin.lecturers.index') }}"
                                    class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 transition-colors text-sm">Hủy
                                    bỏ</a>
                                <button type="submit"
                                    class="px-6 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 shadow-sm flex items-center gap-2 text-sm transition-all active:scale-95">
                                    <span class="material-symbols-outlined !text-[18px]">save</span> Cập nhật
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        // Ở trang Edit không cần script tự động tạo email vì dễ gây lỗi khi sửa tên
    </script>
@endsection
