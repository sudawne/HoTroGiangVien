<header
    class="h-16 flex items-center justify-between px-4 md:px-6 bg-white dark:bg-[#1e1e2d] border-b border-slate-200 dark:border-slate-700 z-10 sticky top-0 flex-shrink-0">

    {{-- KHUNG TÌM KIẾM & MENU TRIGGER --}}
    <div class="flex items-center gap-3 flex-1 w-full md:w-96">
        
        <button @click="sidebarOpen = !sidebarOpen" 
                class="lg:hidden p-2 -ml-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <div class="relative w-full max-w-md hidden sm:block">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined !text-[20px]">search</span>
            </span>
            <input
                class="block w-full pl-10 pr-3 py-2 border border-slate-200 dark:border-slate-600 rounded bg-slate-50 dark:bg-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-shadow"
                placeholder="Tìm kiếm sinh viên, lớp học, văn bản..." type="text" />
        </div>
        
        <button class="sm:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
             <span class="material-symbols-outlined !text-[20px]">search</span>
        </button>
    </div>

    {{-- KHUNG CÔNG CỤ BÊN PHẢI --}}
    <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
        {{-- Nút Thông Báo --}}
        <button class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors">
            <span class="material-symbols-outlined !text-[22px]">notifications</span>
            <span
                class="absolute top-1.5 right-2 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white dark:ring-[#1e1e2d]"></span>
        </button>
    </div>
</header>