<!DOCTYPE html>
<html class="light" lang="vi">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>@yield('title', 'Hệ Thống Quản Lý')</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet" />
    <link href="https://fonts.googleapis.com" rel="preconnect" />
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />

    {{-- Tailwind CSS & Plugins --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- Tailwind Config --}}
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "primary": "#463acb",
                        "background-light": "#f6f6f8",
                        "background-dark": "#14131f",
                        "slate-800": "#1e293b",
                        "slate-500": "#64748b",
                    },
                    fontFamily: {
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.125rem",
                        "lg": "0.25rem",
                        "xl": "0.5rem",
                        "full": "0.75rem"
                    },
                },
            },
        }
    </script>
    <style>
        html {
            font-size: 12px;
        }

        [x-cloak] {
            display: none !important;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @keyframes highlightFade {
            0% {
                background-color: #dcfce7;
            }

            /* green-100 */
            100% {
                background-color: transparent;
            }
        }

        .highlight-row {
            animation: highlightFade 3s ease-out forwards;
        }

        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .animate-fade-in-right {
            animation: fadeInRight 0.3s ease-out;
        }

        .modal-enter {
            opacity: 0;
            transform: scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: scale(1);
            transition: all 0.2s ease-out;
        }

        .modal-leave {
            opacity: 1;
            transform: scale(1);
        }

        .modal-leave-active {
            opacity: 0;
            transform: scale(0.95);
            transition: all 0.2s ease-in;
        }

        .deleted-row {
            opacity: 0.6;
            background-color: #f9fafb;
            /* gray-50 */
            filter: grayscale(100%);
        }

        .deleted-row:hover {
            opacity: 0.9;
            transition: opacity 0.3s;
        }

        /* Animation cho nút bulk action */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .bulk-anim {
            animation: slideIn 0.3s ease-out;
        }
    </style>
</head>

<body
    class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-gray-100 font-display text-sm antialiased overflow-hidden"
    x-data="{ sidebarOpen: false }">

    <div class="flex h-screen w-full overflow-hidden">

        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
            class="fixed inset-0 bg-slate-900/80 z-20 lg:hidden" x-cloak>
        </div>

        {{-- Include Sidebar --}}
        @include('partials.admin.sidebar')

        <main class="flex-1 flex flex-col h-full overflow-hidden relative w-full transition-all duration-300">

            {{-- Include Header --}}
            @include('partials.admin.header')

            {{-- Main Content Area --}}
            <div class="flex-1 overflow-y-auto p-4 md:p-6 scroll-smooth">
                @yield('content')
            </div>

        </main>
    </div>
</body>

</html>
