<!DOCTYPE html>
<html lang="vi" class="light">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Cổng thông tin Sinh viên')</title>

    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link
        href="https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet" />
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#1a365d",
                        "primary-light": "#2a528a",
                        "background-light": "#f0f2f5",
                        "background-dark": "#101622",
                    },
                    fontFamily: {
                        "display": ["Lexend", "sans-serif"],
                        "body": ["Inter", "sans-serif"]
                    },
                },
            },
        }
    </script>
    <style>
        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
    @yield('styles')
</head>

<body class="bg-background-light dark:bg-background-dark font-body text-slate-900 dark:text-slate-100 min-h-screen">

    <div class="relative flex min-h-screen w-full flex-col group/design-root">

        @include('partials.student.header')

        {{-- Đổi Grid để tạo Layout 2 cột: Trái (col-span-4) và Phải (col-span-8) --}}
        {{-- Sửa max-w-[1280px] và gap-6 để tràn đều và thoáng hơn --}}
        <main class="flex-1 w-full max-w-[1280px] mx-auto px-4 sm:px-6 py-6 grid grid-cols-12 gap-6 items-start">

            @include('partials.student.sidebar')

            @yield('content')

        </main>
    </div>

    @include('partials.toast')
    @include('partials.confirm_modal')

    @yield('scripts')
</body>

</html>
