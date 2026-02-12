@extends('layouts.admin')
@section('title', 'Thêm Giảng viên')

@section('content')
    <div class="w-full px-4 py-6">
        {{-- Header --}}
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.lecturers.index') }}"
                class="p-2 bg-white border border-slate-300 rounded-sm text-slate-600 hover:bg-slate-50 shadow-sm">
                <span class="material-symbols-outlined !text-[16px] block">arrow_back</span>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white uppercase">Thêm Giảng viên mới</h1>
                <p class="text-xs text-slate-500">Tạo tài khoản và thông tin giảng viên</p>
            </div>
        </div>

        {{-- Hiển thị lỗi chung --}}
        @if (session('error'))
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700">
                <p class="font-bold">Lỗi!</p>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- Form --}}
        <div class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-sm shadow-sm">
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 bg-slate-50/30">
                <h3 class="font-bold text-slate-800 dark:text-white text-base flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary !text-[16px]">person_add</span> Thông tin chi tiết
                </h3>
            </div>

            {{-- Thêm enctype="multipart/form-data" để upload file --}}
            <form action="{{ route('admin.lecturers.store') }}" method="POST" enctype="multipart/form-data" class="p-6"
                novalidate>
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Cột trái: Thông tin tài khoản --}}
                    <div>
                        <h4 class="text-sm font-bold text-slate-400 uppercase mb-4 border-b pb-2">Thông tin cá nhân</h4>

                        {{-- AVATAR UPLOAD --}}
                        <div class="mb-6 flex flex-col items-center">
                            <div class="relative group cursor-pointer w-32 h-32 mb-3">
                                {{-- Preview Image --}}
                                <img id="avatar-preview"
                                    src="https://ui-avatars.com/api/?name=GV&background=random&size=128"
                                    alt="Avatar Preview"
                                    class="w-full h-full rounded-full object-cover border-4 border-slate-100 shadow-sm group-hover:border-primary/30 transition-all">

                                {{-- Overlay icon camera --}}
                                <div
                                    class="absolute inset-0 bg-black/40 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="material-symbols-outlined text-white">photo_camera</span>
                                </div>

                                {{-- Input ẩn --}}
                                <input type="file" name="avatar" id="avatar-input" accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                    onchange="previewImage(this)">
                            </div>
                            <p class="text-xs text-slate-500">Nhấn vào ảnh để thay đổi (Tối đa 2MB)</p>
                            @error('avatar')
                                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="space-y-5">
                            {{-- Họ tên --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Họ và Tên <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary focus:border-primary text-sm @error('name') border-red-500 @enderror">
                                @error('name')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Email (Tài khoản)</label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                    placeholder="Để trống để tự tạo (vd: vhnhan@vnkgu.edu.vn)"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm @error('email') border-red-500 @enderror">
                                <p class="text-xs text-slate-500 mt-1">Nếu để trống, hệ thống sẽ tự tạo từ Họ tên.</p>
                                @error('email')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Số điện thoại --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Số điện thoại</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm">
                            </div>
                        </div>
                    </div>

                    {{-- Cột phải: Thông tin chuyên môn --}}
                    <div>
                        <h4 class="text-sm font-bold text-slate-400 uppercase mb-4 border-b pb-2">Thông tin công tác</h4>

                        <div class="space-y-5">
                            {{-- Mã Giảng viên --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Mã Giảng viên <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="lecturer_code" value="{{ old('lecturer_code') }}" required
                                        placeholder="VD: GV001"
                                        class="w-full pl-3 pr-10 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary font-mono text-sm uppercase @error('lecturer_code') border-red-500 @enderror">
                                    <span
                                        class="absolute right-3 top-2.5 text-slate-400 material-symbols-outlined !text-[18px]">badge</span>
                                </div>
                                <p class="text-xs text-slate-500 mt-1 italic">Mã này sẽ được dùng làm mật khẩu mặc định.</p>
                                @error('lecturer_code')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- Khoa --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Khoa / Đơn vị <span
                                        class="text-red-500">*</span></label>
                                <select name="department_id" required
                                    class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm cursor-pointer">
                                    <option value="">-- Chọn Khoa --</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                {{-- Học vị --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Học vị</label>
                                    <select name="degree"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm">
                                        <option value="Cử nhân" {{ old('degree') == 'Cử nhân' ? 'selected' : '' }}>Cử nhân
                                        </option>
                                        <option value="Thạc sĩ" {{ old('degree') == 'Thạc sĩ' ? 'selected' : '' }}>Thạc sĩ
                                        </option>
                                        <option value="Tiến sĩ" {{ old('degree') == 'Tiến sĩ' ? 'selected' : '' }}>Tiến sĩ
                                        </option>
                                        <option value="PGS.TS" {{ old('degree') == 'PGS.TS' ? 'selected' : '' }}>PGS.TS
                                        </option>
                                    </select>
                                </div>
                                {{-- Chức vụ --}}
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1.5">Chức vụ</label>
                                    <input type="text" name="position" value="{{ old('position', 'Giảng viên') }}"
                                        class="w-full px-3 py-2.5 border border-slate-300 rounded-sm focus:ring-1 focus:ring-primary text-sm">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-end gap-3">
                    <a href="{{ route('admin.lecturers.index') }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold rounded-sm hover:bg-slate-50 text-sm">Hủy
                        bỏ</a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-primary text-white font-semibold rounded-sm hover:bg-primary/90 shadow-sm flex items-center gap-2 text-sm">
                        <span class="material-symbols-outlined !text-[18px]">save</span> Lưu Giảng viên
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script Preview Ảnh --}}
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
    </script>
@endsection
