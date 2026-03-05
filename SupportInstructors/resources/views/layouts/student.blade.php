<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Cổng Thông Tin Sinh Viên')</title>

    {{-- Google Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS & Alpine.js --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: "#463acb"
                    },
                    fontFamily: {
                        sans: ["Inter", "sans-serif"]
                    },
                }
            }
        }
    </script>

    @yield('styles')
</head>

<body class="bg-[#f0f2f5] text-slate-800 font-sans antialiased">

    {{-- THANH ĐIỀU HƯỚNG TRÊN CÙNG (HEADER) CHO SINH VIÊN --}}
    <header class="bg-white shadow-sm border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-5xl mx-auto px-4 h-14 flex items-center justify-between">
            <div class="flex items-center gap-2 text-primary font-bold text-lg">
                <span class="material-symbols-outlined !text-[28px]">school</span>
                <span>Cổng Sinh Viên</span>
            </div>

            <div class="flex items-center gap-4">
                <div class="font-medium text-sm text-slate-600">
                    Xin chào, {{ Auth::user()->name ?? 'Sinh viên' }}
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit"
                        class="p-2 bg-slate-100 hover:bg-red-50 text-slate-600 hover:text-red-600 rounded-full transition-colors"
                        title="Đăng xuất">
                        <span class="material-symbols-outlined !text-[20px] block">logout</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- NƠI HIỂN THỊ NỘI DUNG CHÍNH (BẢNG TIN SẼ NẰM Ở ĐÂY) --}}
    <main>
        @yield('content')
    </main>

    {{-- NƠI CHỨA CÁC THÔNG BÁO POPUP (NẾU CÓ) --}}
    @include('partials.toast')

    @yield('scripts')
</body>

</html>
