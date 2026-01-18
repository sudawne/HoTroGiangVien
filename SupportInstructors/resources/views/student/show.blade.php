@extends('layouts.admin')
@section('title', 'Hồ sơ sinh viên: ' . $student->fullname)

@section('content')
    <div class="max-w-7xl mx-auto space-y-6" x-data="{ activeTab: 'general' }">

        {{-- HEADER PROFILE --}}
        <div
            class="bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg p-6 flex flex-col md:flex-row gap-6 items-start shadow-sm">
            <div class="relative">
                <img src="{{ $student->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($student->fullname) . '&background=random' }}"
                    class="w-24 h-24 rounded-lg object-cover border-4 border-slate-50 dark:border-slate-800 shadow-sm">
                <span
                    class="absolute -bottom-2 -right-2 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full border-2 border-white dark:border-[#1e1e2d]">
                    {{ Str::upper($student->status) }}
                </span>
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-800 dark:text-white truncate">{{ $student->fullname }}</h1>
                        <div class="flex items-center gap-3 mt-1 text-slate-500 dark:text-slate-400 text-sm">
                            <span class="flex items-center gap-1"><span
                                    class="material-symbols-outlined !text-[16px]">id_card</span>
                                {{ $student->student_code }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span class="flex items-center gap-1"><span
                                    class="material-symbols-outlined !text-[16px]">school</span>
                                {{ $student->class->name ?? 'Chưa phân lớp' }}</span>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button
                            class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded text-sm font-medium hover:bg-slate-50 transition-colors">
                            <span class="material-symbols-outlined !text-[18px]">chat</span>
                            <span class="hidden sm:inline">Nhắn tin</span>
                        </button>
                        <button
                            class="flex items-center gap-2 px-3 py-2 bg-primary text-white rounded text-sm font-medium hover:bg-primary/90 transition-colors shadow-sm">
                            <span class="material-symbols-outlined !text-[18px]">edit</span>
                            <span class="hidden sm:inline">Cập nhật</span>
                        </button>
                    </div>
                </div>

                {{-- Quick Stats --}}
                <div
                    class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">GPA Tích lũy</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white mt-1">3.45 <span
                                class="text-xs font-normal text-slate-400">/ 4.0</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Tín chỉ nợ</p>
                        <p class="text-lg font-bold text-orange-500 mt-1">4 <span
                                class="text-xs font-normal text-slate-400">TC</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">ĐRL (TB)</p>
                        <p class="text-lg font-bold text-emerald-600 mt-1">88</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 uppercase font-semibold">Cảnh báo</p>
                        <p class="text-lg font-bold text-slate-800 dark:text-white mt-1">Không</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- TABS NAVIGATION --}}
        <div class="border-b border-slate-200 dark:border-slate-700">
            <nav class="flex gap-6" aria-label="Tabs">
                <button @click="activeTab = 'general'"
                    :class="activeTab === 'general' ? 'border-primary text-primary' :
                        'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[20px]">person</span> Thông tin chung
                </button>
                <button @click="activeTab = 'academic'"
                    :class="activeTab === 'academic' ? 'border-primary text-primary' :
                        'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[20px]">analytics</span> Kết quả học tập
                </button>
                <button @click="activeTab = 'family'"
                    :class="activeTab === 'family' ? 'border-primary text-primary' :
                        'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[20px]">family_restroom</span> Gia đình & Liên hệ
                </button>
            </nav>
        </div>

        {{-- TAB CONTENT --}}

        {{-- 1. General Info --}}
        <div x-show="activeTab === 'general'" x-transition.opacity class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div
                class="md:col-span-2 bg-white dark:bg-[#1e1e2d] rounded-lg border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Thông tin cá nhân</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8 text-sm">
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 mb-1">Email trường</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $student->user->email }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 mb-1">Số điện thoại</span>
                        <span
                            class="font-medium text-slate-800 dark:text-slate-200">{{ $student->user->phone ?? 'Chưa cập nhật' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 mb-1">Ngày sinh</span>
                        <span
                            class="font-medium text-slate-800 dark:text-slate-200">{{ $student->dob ? \Carbon\Carbon::parse($student->dob)->format('d/m/Y') : '--' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 mb-1">Nơi sinh</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $student->pob ?? '--' }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-500 dark:text-slate-400 mb-1">Niên khóa</span>
                        <span class="font-medium text-slate-800 dark:text-slate-200">{{ $student->enrollment_year }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1e1e2d] rounded-lg border border-slate-200 dark:border-slate-700 p-6 shadow-sm">
                <h3 class="font-bold text-slate-800 dark:text-white mb-4">Ghi chú của Cố vấn</h3>
                <div
                    class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-900/30 rounded p-3 text-sm text-yellow-800 dark:text-yellow-200 mb-3">
                    <div class="flex items-center gap-2 mb-1 font-semibold">
                        <span class="material-symbols-outlined !text-[16px]">warning</span> Cảnh báo
                    </div>
                    Sinh viên có dấu hiệu chán học, nghỉ nhiều môn Toán rời rạc. Cần theo dõi thêm.
                </div>
                <textarea
                    class="w-full text-sm border-slate-200 dark:border-slate-600 rounded bg-slate-50 dark:bg-slate-800 p-2 focus:ring-primary"
                    rows="3" placeholder="Thêm ghi chú mới..."></textarea>
                <button
                    class="w-full mt-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold py-2 rounded transition-colors">Lưu
                    ghi chú</button>
            </div>
        </div>

        {{-- 2. Academic Info (Results & Debts) --}}
        <div x-show="activeTab === 'academic'" x-cloak class="space-y-6">

            <div
                class="bg-white dark:bg-[#1e1e2d] rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                <div
                    class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center bg-red-50/30">
                    <h3 class="font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-red-500">money_off</span> Các học phần đang nợ
                    </h3>
                    <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded">2 Môn</span>
                </div>
                <table class="w-full text-sm text-left">
                    <thead
                        class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 font-semibold border-b border-slate-200 dark:border-slate-700">
                        <tr>
                            <th class="px-6 py-3">Mã HP</th>
                            <th class="px-6 py-3">Tên học phần</th>
                            <th class="px-6 py-3">Số TC</th>
                            <th class="px-6 py-3">Học kỳ</th>
                            <th class="px-6 py-3 text-right">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        <tr>
                            <td class="px-6 py-3 font-mono text-slate-600">MATH101</td>
                            <td class="px-6 py-3 font-medium">Toán cao cấp A1</td>
                            <td class="px-6 py-3">3</td>
                            <td class="px-6 py-3">HK1 2023-2024</td>
                            <td class="px-6 py-3 text-right text-red-500 font-bold">Chưa trả</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-3 font-mono text-slate-600">PHY102</td>
                            <td class="px-6 py-3 font-medium">Vật lý đại cương</td>
                            <td class="px-6 py-3">2</td>
                            <td class="px-6 py-3">HK2 2023-2024</td>
                            <td class="px-6 py-3 text-right text-red-500 font-bold">Chưa trả</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3. Family Info (Table student_relatives) --}}
        <div x-show="activeTab === 'family'" x-cloak>
            <div
                class="bg-white dark:bg-[#1e1e2d] rounded-lg border border-slate-200 dark:border-slate-700 overflow-hidden shadow-sm">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 dark:text-white">Thông tin gia đình</h3>
                    <button class="text-primary text-sm font-medium hover:underline">+ Thêm người thân</button>
                </div>
                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Relative Card 1 --}}
                    <div class="border border-slate-200 dark:border-slate-700 rounded p-4 flex gap-4 items-start">
                        <div class="bg-slate-100 dark:bg-slate-700 p-2 rounded-full text-slate-500">
                            <span class="material-symbols-outlined">diversity_1</span>
                        </div>
                        <div>
                            <p class="font-bold text-slate-800 dark:text-white">Nguyễn Văn B</p>
                            <p class="text-xs text-slate-500 uppercase font-semibold mt-0.5">Bố đẻ</p>
                            <div class="mt-3 text-sm space-y-1">
                                <p class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                    <span class="material-symbols-outlined !text-[16px]">call</span> 0909 123 456
                                    <span
                                        class="bg-red-100 text-red-600 text-[10px] font-bold px-1.5 py-0.5 rounded">SOS</span>
                                </p>
                                <p class="flex items-start gap-2 text-slate-600 dark:text-slate-300">
                                    <span class="material-symbols-outlined !text-[16px] mt-0.5">home</span>
                                    123 Đường ABC, Phường X, Quận Y, TP.HCM
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
