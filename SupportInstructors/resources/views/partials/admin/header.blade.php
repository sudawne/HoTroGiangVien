@php
    // LOGIC TÍNH TOÁN THÔNG BÁO NGAY TẠI VIEW
    $alerts = collect();
    $user = Auth::user();

    if ($user && $user->role_id == 1) {
        // Chỉ lấy cho Admin
        // 1. Bài viết chờ duyệt
        $pendings = \App\Models\Notification::with('sender')
            ->where('status', 'pending')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => 'p_' . $item->id,
                    'title' => 'Yêu cầu duyệt bài',
                    'message' =>
                        'Giảng viên <b>' . ($item->sender->name ?? 'Ai đó') . '</b> đã gửi một bài đăng cần bạn duyệt.',
                    'time' => $item->created_at,
                    'url' => route('admin.notifications.show', $item->id),
                    'icon' => 'hourglass_empty',
                    'color' => 'text-amber-600',
                    'bg' => 'bg-amber-100',
                ];
            });
        $alerts = $alerts->merge($pendings);

        // 2. Bình luận mới vào bài của Admin
        $comments = \App\Models\NotificationComment::with(['user', 'notification'])
            ->whereHas('notification', function ($q) use ($user) {
                $q->where('sender_id', $user->id);
            })
            ->where('user_id', '!=', $user->id) // Không lấy comment của chính mình
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => 'c_' . $item->id,
                    'title' => 'Bình luận mới',
                    'message' =>
                        '<b>' .
                        $item->user->name .
                        '</b> đã bình luận: "' .
                        \Illuminate\Support\Str::limit($item->content, 30) .
                        '"',
                    'time' => $item->created_at,
                    'url' => route('admin.notifications.show', $item->notification_id) . '#comment-' . $item->id,
                    'icon' => 'chat',
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-100',
                ];
            });
        $alerts = $alerts->merge($comments);

        // 3. Lượt thích mới vào bài của Admin
        $likes = \App\Models\NotificationLike::with(['user', 'notification'])
            ->whereHas('notification', function ($q) use ($user) {
                $q->where('sender_id', $user->id);
            })
            ->where('user_id', '!=', $user->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'id' => 'l_' . $item->id,
                    'title' => 'Lượt thích mới',
                    'message' => '<b>' . $item->user->name . '</b> đã thích bài viết của bạn.',
                    'time' => $item->created_at,
                    'url' => route('admin.notifications.show', $item->notification_id),
                    'icon' => 'favorite',
                    'color' => 'text-rose-600',
                    'bg' => 'bg-rose-100',
                ];
            });
        $alerts = $alerts->merge($likes);
    }

    // Sắp xếp theo thời gian mới nhất và chỉ lấy 15 dòng
    $alerts = $alerts->sortByDesc('time')->values()->take(15);

    // LẤY THỜI GIAN CHECK CUỐI CÙNG TỪ DATABASE
    $lastCheck = $user->last_alert_check
        ? \Carbon\Carbon::parse($user->last_alert_check)
        : \Carbon\Carbon::createFromTimestamp(0);
    $unreadCount = $alerts->where('time', '>', $lastCheck)->count();
@endphp

<header
    class="h-16 flex items-center justify-between px-4 md:px-6 bg-white dark:bg-[#1e1e2d] border-b border-slate-200 dark:border-slate-700 z-50 sticky top-0 flex-shrink-0">

    {{-- KHUNG TÌM KIẾM & MENU TRIGGER (LAYOUT CŨ CỦA BẠN) --}}
    <div class="flex items-center gap-3 flex-1 w-full md:w-96">
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 -ml-2 text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 rounded transition-colors">
            <span class="material-symbols-outlined !text-[18px]">menu</span>
        </button>

        <div class="relative w-full max-w-md hidden sm:block">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined !text-[15px]">search</span>
            </span>
            <input
                class="block w-full pl-9 pr-3 py-2 border border-slate-200 dark:border-slate-600 rounded bg-slate-50 dark:bg-slate-800 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-shadow"
                placeholder="Tìm kiếm sinh viên, lớp học, văn bản..." type="text" />
        </div>

        <button class="sm:hidden p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded">
            <span class="material-symbols-outlined !text-[15px]">search</span>
        </button>
    </div>

    {{-- KHUNG CÔNG CỤ BÊN PHẢI --}}
    <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">

        {{-- Nút Kiểm tra hệ thống --}}
        <form action="{{ route('admin.system.check') ?? '#' }}" method="POST" class="inline-block"
            id="system-check-form">
            @csrf
            <button type="button" onclick="runCheck()" title="Kiểm tra / Rà soát hệ thống"
                class="group relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors">
                <span id="icon-check"
                    class="material-symbols-outlined !text-[18px] group-hover:text-primary transition-colors">fact_check</span>
                <span id="icon-loading"
                    class="material-symbols-outlined !text-[18px] text-primary animate-spin hidden">sync</span>
            </button>
        </form>

        {{-- Nút Thông báo --}}
        <div x-data="{ alertOpen: false, unread: {{ $unreadCount }} }" class="relative">
            <button @click="alertOpen = !alertOpen" @click.away="alertOpen = false"
                class="relative p-2 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 rounded transition-colors focus:outline-none">

                <span class="material-symbols-outlined !text-[17px]">notifications</span>

                {{-- BADGE --}}
                <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" x-transition
                    class="absolute top-1.5 right-1 min-w-[14px] h-[14px] px-[3px] rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center ring-[1.5px] ring-white dark:ring-[#1e1e2d]"
                    x-cloak>
                </span>
            </button>

            {{-- Dropdown Thông báo --}}
            <div x-show="alertOpen" x-transition
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50"
                x-cloak>
                <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-base">Thông báo</h3>

                    {{-- CHUYỂN LOGIC FETCH GỌI API VÀO ĐÂY --}}
                    <span class="text-xs text-primary font-semibold cursor-pointer hover:underline"
                        @click="
                            if(unread > 0) {
                                fetch('{{ route('admin.alerts.mark_read') }}', { 
                                    method: 'POST', 
                                    headers: { 
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json'
                                    } 
                                });
                                unread = 0;
                            }
                        ">Đã
                        đọc tất cả</span>
                </div>
                <div class="max-h-[400px] overflow-y-auto overscroll-contain custom-scrollbar">
                    @forelse ($alerts as $alert)
                        <a href="{{ $alert->url }}"
                            @click.prevent="
                                let targetUrl = '{{ $alert->url }}';
                                // Nếu thông báo này mới hơn lần check cuối (hiển thị màu xanh)
                                if ({{ $alert->time > $lastCheck ? 'true' : 'false' }}) {
                                    fetch('{{ route('admin.alerts.mark_read') }}', { 
                                        method: 'POST', 
                                        headers: { 
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Content-Type': 'application/json',
                                            'Accept': 'application/json'
                                        },
                                        keepalive: true // Đảm bảo API vẫn chạy khi trình duyệt nhảy trang
                                    });
                                }
                                // Chuyển trang sau khi gọi API
                                window.location.href = targetUrl;
                            "
                            class="flex items-start gap-4 p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors relative {{ $alert->time > $lastCheck ? 'bg-blue-50/30' : '' }}">

                            @if ($alert->time > $lastCheck)
                                <div
                                    class="absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full">
                                </div>
                            @endif

                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $alert->bg }} {{ $alert->color }}">
                                <span class="material-symbols-outlined !text-[20px]">{{ $alert->icon }}</span>
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] text-slate-700 leading-snug">{!! $alert->message !!}</p>
                                <p class="text-[11px] font-medium text-blue-600 mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-[12px]">schedule</span>
                                    {{ $alert->time->diffForHumans() }}
                                </p>
                            </div>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-400">
                            <span
                                class="material-symbols-outlined !text-[40px] opacity-50 mb-2 block">notifications_paused</span>
                            <p class="text-sm font-medium">Bạn không có thông báo mới.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    function runCheck() {
        if (typeof showConfirm === 'function') {
            showConfirm(
                'Rà soát hệ thống',
                'Hệ thống sẽ kiểm tra và tự động tạo <strong>Năm học mới</strong>.<br>Quá trình này an toàn với dữ liệu hiện tại.',
                function() {
                    document.getElementById('icon-check').classList.add('hidden');
                    document.getElementById('icon-loading').classList.remove('hidden');
                    document.getElementById('system-check-form').submit();
                }
            );
        } else {
            if (confirm('Chạy rà soát hệ thống ngay?')) {
                document.getElementById('icon-check').classList.add('hidden');
                document.getElementById('icon-loading').classList.remove('hidden');
                document.getElementById('system-check-form').submit();
            }
        }
    }
</script>
