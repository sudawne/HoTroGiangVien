<aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-white dark:bg-[#1e1e2d] border-r border-slate-200 dark:border-slate-700 flex flex-col transition-transform duration-300 transform lg:static lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
    {{-- LOGO --}}
    <div
        class="h-16 flex items-center justify-between px-5 border-b border-slate-100 dark:border-slate-800 flex-shrink-0">
        <div class="flex items-center gap-3">
            <div class="bg-primary/10 p-1.5 rounded text-primary">
                {{-- Icon Logo: 20px --}}
                <span class="material-symbols-outlined !text-[20px]">school</span>
            </div>
            <div>
                <h1 class="font-bold text-slate-800 dark:text-white leading-tight">Cố Vấn Học Tập</h1>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">Đại học Kiên Giang</p>
            </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-slate-500 hover:text-red-500 transition-colors">
            <span class="material-symbols-outlined">close</span>
        </button>
    </div>

    {{-- MENU CHÍNH --}}
    <nav class="flex-1 overflow-y-auto py-4 px-3 flex flex-col gap-1">

        {{-- TỔNG QUAN --}}
        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors group
            {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.dashboard') }}">
            {{-- Icon Menu: 16px --}}
            <span class="material-symbols-outlined !text-[16px] font-medium"
                data-weight="{{ request()->routeIs('admin.dashboard') ? 'fill' : 'regular' }}">dashboard</span>
            <span class="font-semibold text-sm">Tổng quan</span>
        </a>

        {{-- QUẢN LÝ --}}
        <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quản lý</div>

        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors
            {{ request()->routeIs('admin.classes.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.classes.index') }}">
            <span class="material-symbols-outlined !text-[16px]">groups</span>
            <span class="font-medium text-sm">Quản lý Lớp học</span>
        </a>


        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors
            {{ request()->routeIs('admin.lecturers.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.lecturers.index') }}">
            <span class="material-symbols-outlined !text-[16px]">supervisor_account</span>
            <span class="font-medium text-sm">Quản lý Giảng viên</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors
            {{ request()->routeIs('admin.students.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.students.index') }}">
            <span class="material-symbols-outlined !text-[16px]">badge</span>
            <span class="font-medium text-sm">Hồ sơ Sinh viên</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
            href="#">
            <span class="material-symbols-outlined !text-[16px]">analytics</span>
            <span class="font-medium text-sm">Kết quả học tập</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
            href="#">
            <span class="material-symbols-outlined !text-[16px]">star</span>
            <span class="font-medium text-sm">Điểm rèn luyện</span>
        </a>

        {{-- BÁO CÁO --}}
        <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Báo cáo</div>

        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors
            {{ request()->routeIs('admin.academic_warnings.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.academic_warnings.index') }}">
            <span class="material-symbols-outlined !text-[16px]"
                data-weight="{{ request()->routeIs('admin.academic_warnings.*') ? 'fill' : 'regular' }}">warning</span>
            <span class="font-medium text-sm">Cảnh cáo học tập</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
            href="#">
            <span class="material-symbols-outlined !text-[16px]">emoji_events</span>
            <span class="font-medium text-sm">Thống kê Học bổng</span>
        </a>

        {{-- TƯƠNG TÁC --}}
        <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-slate-400 uppercase tracking-wider">Tương tác</div>

        <a class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
            href="#">
            <span class="material-symbols-outlined !text-[16px]">campaign</span>
            <span class="font-medium text-sm">Thông báo & Tin tức</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded transition-colors
            {{ request()->routeIs('admin.minutes.*') ? 'bg-primary/10 text-primary' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white' }}"
            href="{{ route('admin.minutes.index') }}">
            <span class="material-symbols-outlined !text-[16px]">description</span>
            <span class="font-medium text-sm">Biên bản họp</span>
        </a>

        <a class="flex items-center gap-3 px-3 py-2 rounded text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white transition-colors"
            href="#">
            <span class="material-symbols-outlined !text-[16px]">forum</span>
            <span class="font-medium text-sm">Trao đổi / Chat</span>
        </a>

        {{-- USER PROFILE & ĐĂNG XUẤT --}}
        <div class="mt-auto pt-4 border-t border-slate-100 dark:border-slate-800 relative group">

            <div
                class="absolute bottom-full left-0 w-full mb-2 bg-white dark:bg-[#1e1e2d] border border-slate-200 dark:border-slate-700 rounded-lg shadow-lg overflow-hidden invisible opacity-0 group-hover:visible group-hover:opacity-100 transition-all duration-200">
                <a href="#"
                    class="flex items-center gap-3 px-4 py-3 hover:bg-slate-50 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-300 transition-colors">
                    <span class="material-symbols-outlined !text-[16px]">settings</span>
                    <span class="text-sm font-medium">Hệ thống</span>
                </a>
                <div class="h-[1px] bg-slate-100 dark:bg-slate-700 mx-2"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 transition-colors">
                        <span class="material-symbols-outlined !text-[16px]">logout</span>
                        <span class="text-sm font-medium">Đăng xuất</span>
                    </button>
                </form>
            </div>

            <button
                class="flex items-center gap-3 w-full p-2 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors text-left">
                <div class="size-9 rounded bg-slate-200 overflow-hidden border border-slate-300 flex-shrink-0"
                    style="background-image: url('https://cdn4.iconfinder.com/data/icons/free-large-boss-icon-set/512/Admin.png'); background-size: cover;">
                </div>
                <div class="flex-1 overflow-hidden">
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 leading-none truncate">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1 truncate">
                        {{ optional(Auth::user()->role)->name == 'ADMIN' ? 'Quản trị viên' : 'Giảng viên' }}
                    </p>
                </div>
                <span class="material-symbols-outlined text-slate-400 !text-[16px]">expand_less</span>
            </button>
        </div>
    </nav>
</aside>
