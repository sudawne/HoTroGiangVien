@php
    $user = Auth::user();
    $userId = $user ? $user->id : 0;

    // LẤY DANH SÁCH MÃ THÔNG BÁO ĐÃ ĐỌC
    $readAlerts = \DB::table('user_read_alerts')->where('user_id', $userId)->pluck('alert_id')->toArray();

    $allAlerts = collect();

    if ($user && $user->role_id == 1) {
        // 1. Bài viết
        $pendings = \App\Models\Notification::with('sender')
            ->where('status', 'pending')
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($item) use ($readAlerts) {
                $alertId = 'p_' . $item->id;
                return (object) [
                    'type' => 'post',
                    'id' => $alertId,
                    'notification_id' => $item->id,
                    'post_title' => 'Duyệt bài đăng mới',
                    'message' =>
                        'Giảng viên <b>' . ($item->sender->name ?? 'Ai đó') . '</b> đã gửi một bài đăng cần bạn duyệt.',
                    'time' => $item->created_at,
                    'url' => route('admin.notifications.show', $item->id),
                    'icon' => 'hourglass_empty',
                    'color' => 'text-amber-600',
                    'bg' => 'bg-amber-100',
                    'is_read' => in_array($alertId, $readAlerts),
                ];
            });
        $allAlerts = $allAlerts->merge($pendings);

        // 2. Bình luận
        $comments = \App\Models\NotificationComment::with(['user', 'notification'])
            ->whereHas('notification', fn($q) => $q->where('sender_id', $userId))
            ->where('user_id', '!=', $userId)
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($item) use ($readAlerts) {
                $alertId = 'c_' . $item->id;
                return (object) [
                    'type' => 'comment',
                    'id' => $alertId,
                    'notification_id' => $item->notification_id,
                    'post_title' => $item->notification->title,
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
                    'is_read' => in_array($alertId, $readAlerts),
                ];
            });
        $allAlerts = $allAlerts->merge($comments);

        // 3. Lượt thích
        $likes = \App\Models\NotificationLike::with(['user', 'notification'])
            ->whereHas('notification', fn($q) => $q->where('sender_id', $userId))
            ->where('user_id', '!=', $userId)
            ->latest()
            ->take(30)
            ->get()
            ->map(function ($item) use ($readAlerts) {
                $alertId = 'l_' . $item->id;
                return (object) [
                    'type' => 'like',
                    'id' => $alertId,
                    'notification_id' => $item->notification_id,
                    'post_title' => $item->notification->title,
                    'message' => '<b>' . $item->user->name . '</b> đã thích bài viết của bạn.',
                    'time' => $item->created_at,
                    'url' => route('admin.notifications.show', $item->notification_id),
                    'icon' => 'favorite',
                    'color' => 'text-rose-600',
                    'bg' => 'bg-rose-100',
                    'is_read' => in_array($alertId, $readAlerts),
                ];
            });
        $allAlerts = $allAlerts->merge($likes);
    }

    $allAlerts = $allAlerts->sortByDesc('time')->values();
    $unreadCount = $allAlerts->where('is_read', false)->count();
    $allAlertIds = $allAlerts->pluck('id')->values()->toJson();

    $groupedAlerts = $allAlerts
        ->groupBy('notification_id')
        ->map(function ($group) {
            return (object) [
                'notification_id' => $group->first()->notification_id,
                'post_title' => $group->first()->post_title,
                'latest_time' => $group->first()->time,
                'unread_count' => $group->where('is_read', false)->count(),
                'items' => $group,
            ];
        })
        ->sortByDesc('latest_time')
        ->values();
@endphp

<header
    class="h-16 flex items-center justify-between px-4 md:px-6 bg-white dark:bg-[#1e1e2d] border-b border-slate-200 dark:border-slate-700 z-50 sticky top-0 flex-shrink-0">
    <div class="flex items-center gap-3 flex-1 w-full md:w-96">
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 -ml-2 text-slate-600 hover:bg-slate-100 rounded transition-colors">
            <span class="material-symbols-outlined !text-[18px]">menu</span>
        </button>
        <div class="relative w-full max-w-md hidden sm:block">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                <span class="material-symbols-outlined !text-[15px]">search</span>
            </span>
            <input
                class="block w-full pl-9 pr-3 py-2 border border-slate-200 rounded bg-slate-50 text-sm placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-shadow"
                placeholder="Tìm kiếm sinh viên, lớp học..." type="text" />
        </div>
    </div>

    <div class="flex items-center gap-2 md:gap-4 flex-shrink-0">
        <form action="{{ route('admin.system.check') ?? '#' }}" method="POST" class="inline-block"
            id="system-check-form">
            @csrf
            <button type="button" onclick="runCheck()" title="Kiểm tra hệ thống"
                class="group relative p-2 text-slate-500 hover:bg-slate-100 rounded transition-colors">
                <span id="icon-check"
                    class="material-symbols-outlined !text-[18px] group-hover:text-primary transition-colors">fact_check</span>
                <span id="icon-loading"
                    class="material-symbols-outlined !text-[18px] text-primary animate-spin hidden">sync</span>
            </button>
        </form>

        {{-- KHU VỰC THÔNG BÁO VỚI LOGIC JS NÂNG CAO --}}
        <div x-data="{
            alertOpen: false,
            unread: {{ $unreadCount }},
            tab: 'all',
            expandedPost: null,
        
            handleAlertClick(alertId, targetUrl) {
                let row = document.getElementById('alert-row-' + alertId) || document.getElementById('alert-row-' + alertId + '-group');
                let isRead = row ? row.getAttribute('data-is-read') === 'true' : true;
        
                let goToComment = () => {
                    this.alertOpen = false; // Đóng popup
                    let currentPath = window.location.pathname;
                    let targetObj = new URL(targetUrl, window.location.origin);
        
                    // KIỂM TRA: NẾU ĐANG Ở CÙNG 1 TRANG BÀI VIẾT
                    if (currentPath === targetObj.pathname) {
                        if (window.history.pushState) {
                            window.history.pushState(null, null, targetObj.hash); // Update URL ko reload
                        } else {
                            window.location.hash = targetObj.hash;
                        }
        
                        if (targetObj.hash) {
                            window.highlightCommentBubble(targetObj.hash); // Cuộn và Highlight
                        } else {
                            window.location.reload();
                        }
                    } else {
                        // NẾU Ở TRANG KHÁC THÌ CHUYỂN TRANG
                        window.location.href = targetUrl;
                    }
                };
        
                if (!isRead && row) {
                    row.setAttribute('data-is-read', 'true');
                    row.classList.remove('bg-blue-50/30', 'alert-unread-bg');
                    let dot = row.querySelector('.unread-dot');
                    if (dot) dot.remove();
                    if (this.unread > 0) this.unread--;
        
                    // Gửi API đánh dấu đọc
                    fetch('{{ route('admin.alerts.mark_read') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ alert_id: alertId })
                    }).then(() => goToComment()).catch(() => goToComment());
                } else {
                    goToComment();
                }
            }
        }" @click.away="alertOpen = false" class="relative">

            <button @click="alertOpen = !alertOpen"
                class="relative p-2 text-slate-500 hover:bg-slate-100 rounded transition-colors focus:outline-none">
                <span class="material-symbols-outlined !text-[17px]">notifications</span>
                <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" x-transition
                    class="absolute top-1.5 right-1 min-w-[14px] h-[14px] px-[3px] rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center ring-[1.5px] ring-white"
                    x-cloak></span>
            </button>

            <div x-show="alertOpen" x-transition
                class="absolute right-0 mt-2 w-80 sm:w-[420px] bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50"
                x-cloak>
                <div class="px-5 py-3 flex justify-between items-center bg-slate-50/50">
                    <h3 class="font-bold text-slate-800 text-base">Thông báo</h3>
                    <span class="text-xs text-primary font-semibold cursor-pointer hover:underline"
                        @click="
                            if(unread > 0) {
                                fetch('{{ route('admin.alerts.mark_read_all') }}', { 
                                    method: 'POST', 
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ alert_ids: {{ $allAlertIds }} })
                                }).then(() => {
                                    unread = 0;
                                    document.querySelectorAll('.alert-unread-bg').forEach(el => {
                                        el.classList.remove('bg-blue-50/30', 'alert-unread-bg');
                                        el.setAttribute('data-is-read', 'true');
                                    });
                                    document.querySelectorAll('.unread-dot').forEach(el => el.remove());
                                    document.querySelectorAll('.unread-badge').forEach(el => el.remove());
                                });
                            }">Đã
                        đọc tất cả</span>
                </div>

                <div class="flex border-b border-slate-200 px-2">
                    <button @click.prevent="tab = 'all'"
                        :class="tab === 'all' ? 'border-primary text-primary font-bold' :
                            'border-transparent text-slate-500 font-medium'"
                        class="flex-1 py-2.5 text-[13px] border-b-2 transition-colors">Tất cả thông báo</button>
                    <button @click.prevent="tab = 'group'"
                        :class="tab === 'group' ? 'border-primary text-primary font-bold' :
                            'border-transparent text-slate-500 font-medium'"
                        class="flex-1 py-2.5 text-[13px] border-b-2 transition-colors">Theo bài viết</button>
                </div>

                <div class="max-h-[450px] overflow-y-auto overscroll-contain custom-scrollbar">

                    {{-- TAB 1 --}}
                    <div x-show="tab === 'all'" class="flex flex-col">
                        @forelse ($allAlerts as $alert)
                            <a href="{{ $alert->url }}" id="alert-row-{{ $alert->id }}"
                                data-is-read="{{ $alert->is_read ? 'true' : 'false' }}"
                                @click.prevent="handleAlertClick('{{ $alert->id }}', '{{ $alert->url }}')"
                                class="alert-unread-bg flex items-start gap-4 p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/30' : '' }}">

                                @if (!$alert->is_read)
                                    <div
                                        class="unread-dot absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full">
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

                    {{-- TAB 2 --}}
                    <div x-show="tab === 'group'" style="display: none;"
                        class="flex flex-col bg-slate-50 border-b border-slate-200">
                        @forelse ($groupedAlerts as $group)
                            <div class="border-b border-slate-200/60 bg-white">
                                <div @click.prevent="expandedPost = expandedPost === {{ $group->notification_id }} ? null : {{ $group->notification_id }}"
                                    class="flex justify-between items-center p-3 cursor-pointer hover:bg-slate-50 transition-colors">
                                    <div class="flex-1 min-w-0 pr-3">
                                        <h4 class="text-[13px] font-bold text-slate-800 truncate">
                                            <span
                                                class="material-symbols-outlined !text-[15px] text-slate-400 align-middle mr-1">feed</span>
                                            {{ $group->post_title ?? 'Bài viết không xác định' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 mt-1">{{ $group->items->count() }} hoạt
                                            động gần đây</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($group->unread_count > 0)
                                            <span
                                                class="unread-badge bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">{{ $group->unread_count }}
                                                mới</span>
                                        @endif
                                        <span
                                            class="material-symbols-outlined text-slate-400 transition-transform duration-200"
                                            :class="expandedPost === {{ $group->notification_id }} ? 'rotate-180' : ''">expand_more</span>
                                    </div>
                                </div>

                                <div x-show="expandedPost === {{ $group->notification_id }}" x-collapse
                                    class="bg-slate-50/50 border-t border-slate-100">
                                    @foreach ($group->items as $alert)
                                        <a href="{{ $alert->url }}" id="alert-row-{{ $alert->id }}-group"
                                            data-is-read="{{ $alert->is_read ? 'true' : 'false' }}"
                                            @click.prevent="handleAlertClick('{{ $alert->id }}', '{{ $alert->url }}')"
                                            class="alert-unread-bg flex items-start gap-3 p-3 border-b border-slate-100 hover:bg-slate-100 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/30' : '' }}">

                                            @if (!$alert->is_read)
                                                <div
                                                    class="unread-dot absolute left-1 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full">
                                                </div>
                                            @endif

                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 ml-2 {{ $alert->bg }} {{ $alert->color }}">
                                                <span
                                                    class="material-symbols-outlined !text-[16px]">{{ $alert->icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[12px] text-slate-700 leading-snug">
                                                    {!! $alert->message !!}</p>
                                                <p class="text-[10px] font-medium text-blue-500 mt-1">
                                                    {{ $alert->time->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center text-slate-400 bg-white">
                                <p class="text-sm font-medium">Chưa có bài viết nào được tương tác.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

{{-- HÀM SCROLL VÀ HIGHLIGHT XUYÊN BG-WHITE --}}
<script>
    window.highlightCommentBubble = function(hash) {
        let el = document.querySelector(hash);
        if (!el) return;

        // 1. Nếu comment đang nằm trong bài viết ẩn -> Mở bài viết ra
        let art = el.closest('article');
        if (art && art.__x) art.__x.$data.showComments = true;

        // 2. Nếu comment đang nằm trong reply con -> Mở danh sách reply con ra
        let replyWrap = el.closest('.replies-wrap');
        if (replyWrap) {
            let parentComment = replyWrap.closest('[id^="comment-"]');
            if (parentComment && parentComment.__x) {
                parentComment.__x.$data.showAllReplies = true;
            }
        }

        // 3. Đợi DOM cập nhật, sau đó Cuộn và Tô Sáng MẠNH MẼ
        setTimeout(() => {
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Tìm chính xác thẻ div có background trắng bên trong để tô màu (Bỏ qua lớp cha bị đè)
            let bubble = el.querySelector('.bg-white, .bg-slate-50, .bg-slate-100') || el;

            let originalBg = bubble.style.backgroundColor;
            let originalTransition = bubble.style.transition;

            // Xóa transition để nháy màu lập tức
            bubble.style.transition = 'none';
            bubble.style.backgroundColor = '#dbeafe'; // Màu xanh nhạt (Blue-100) để thu hút mắt nhìn

            void bubble.offsetWidth; // Cú lừa trình duyệt vẽ lại DOM ngay lập tức

            // Sau 400ms thì từ từ phai về màu cũ
            setTimeout(() => {
                bubble.style.transition = 'background-color 2s ease-in-out';
                bubble.style.backgroundColor = originalBg;

                // Trả lại nguyên trạng
                setTimeout(() => {
                    bubble.style.transition = originalTransition;
                    bubble.style.backgroundColor = '';
                }, 2000);
            }, 400);
        }, 150);
    };

    // Bắt sự kiện khi load từ TRANG KHÁC bay vào
    document.addEventListener('DOMContentLoaded', function() {
        if (window.location.hash && window.location.hash.startsWith('#comment-')) {
            setTimeout(() => {
                window.highlightCommentBubble(window.location.hash);
            }, 300);
        }
    });

    function runCheck() {
        if (typeof showConfirm === 'function') {
            showConfirm('Rà soát hệ thống', 'Hệ thống sẽ tự động tạo Năm học mới.', function() {
                document.getElementById('icon-check').classList.add('hidden');
                document.getElementById('icon-loading').classList.remove('hidden');
                document.getElementById('system-check-form').submit();
            });
        }
    }
</script>
