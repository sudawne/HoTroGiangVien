<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Trường Đại học Kiên Giang - Đăng nhập')</title>

    {{-- Tailwind + plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography"></script>

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />

    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        primary: "#004B8D",
                        "primary-hover": "#003666",
                        "background-light": "#F0F8FF",
                        "background-dark": "#121212",
                        "surface-light": "#FFFFFF",
                        "surface-dark": "#1E1E1E",
                        "text-light": "#333333",
                        "text-dark": "#E0E0E0",
                        "accent-red": "#D32F2F",
                    },
                    fontFamily: {
                        sans: ["Roboto", "sans-serif"],
                    },
                    boxShadow: {
                        'card': '0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06)',
                        'login': '0 10px 25px -5px rgba(0,0,0,0.15), 0 8px 10px -6px rgba(0,0,0,0.1)',
                    }
                },
            },
        };
    </script>

    @stack('head')
</head>

<body class="bg-background-light dark:bg-background-dark font-sans flex flex-col min-h-screen">

    {{-- Header --}}
    <header class="bg-primary w-full shadow-md z-10">
        <div class="container mx-auto px-1 py-3 flex items-center">

            <div class="flex items-center space-x-4 relative">

                {{-- Glow trắng phía sau logo --}}
                <div
                    class="absolute -left-6 top-1/2 -translate-y-1/2 w-32 h-32
                        bg-[radial-gradient(circle,rgba(255,255,255,0.85)_0%,rgba(255,255,255,0.4)_85%,transparent_70%)]
                        blur-xl">
                </div>

                {{-- Logo --}}
                <div class="relative w-16 h-16 flex items-center justify-center">
                    <img src="{{ asset('images/defaults/Logo.png') }}" alt="Logo Trường Đại học Kiên Giang"
                        class="w-full h-full object-contain">
                </div>

                {{-- Text --}}
                <div class="text-white relative">
                    <h1 class="text-2xl font-bold uppercase tracking-wide leading-tight">
                        Trường Đại Học Kiên Giang
                    </h1>
                    <h2 class="text-lg font-light opacity-90 tracking-wider">
                        HỆ THỐNG HỔ TRỢ GIẢNG VIÊN
                    </h2>
                </div>

            </div>

        </div>
    </header>


    {{-- Content --}}
    <main class="flex-grow container mx-auto px-20 py-12 flex flex-col md:flex-row justify-center items-start gap-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-surface-light dark:bg-surface-dark border-t border-gray-200 dark:border-gray-700 mt-auto py-3">
        <div class="container mx-auto px-4 text-center">
            <div class="space-y-1 text-sm text-gray-600 dark:text-gray-400">
                <p class="font-bold text-gray-800 dark:text-gray-200">Trường Đại Học Kiên Giang (Kien Giang University)
                </p>
                <p>Địa chỉ: Số 320 A - Quốc lộ 61 - Châu Thành - An Giang</p>
                <p>Điện thoại: 0297.3.926714 - Fax: 0297.3.926714</p>
            </div>
        </div>
    </footer>

    {{-- Dark mode toggle --}}
    <button
        class="fixed bottom-4 right-4 bg-primary text-white p-3 rounded-full shadow-lg hover:bg-primary-hover focus:outline-none z-50"
        onclick="document.documentElement.classList.toggle('dark')" title="Toggle Dark Mode">
        <span class="material-icons">brightness_4</span>
    </button>

    @stack('scripts')
</body>

</html>
