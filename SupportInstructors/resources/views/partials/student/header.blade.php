@php
    $user = Auth::user();
    $userId = $user ? $user->id : 0;
    $student = $user->student ?? null;
    $classId = $student ? $student->class_id : null;

    // LẤY DANH SÁCH MÃ THÔNG BÁO ĐÃ ĐỌC
    $readAlerts = \DB::table('user_read_alerts')->where('user_id', $userId)->pluck('alert_id')->toArray();
    $allAlerts = collect();

    if ($user) {
        // 1. THÔNG BÁO BÀI VIẾT MỚI
        $posts = \App\Models\Notification::with('sender')
            ->where('status', 'approved')
            ->where(function ($q) use ($classId) {
                $q->where('target_audience', 'all');
                if ($classId) {
                    $q->orWhere(function ($sub) use ($classId) {
                        $sub->where('target_audience', 'class')->whereHas('classes', function ($query) use ($classId) {
                            $query->where('classes.id', $classId);
                        });
                    });
                }
            })
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($item) use ($readAlerts) {
                $alertId = 'n_' . $item->id;
                return (object) [
                    'type' => 'post',
                    'id' => $alertId,
                    'notification_id' => $item->id,
                    'post_title' => $item->title,
                    'message' => '<b>' . ($item->sender->name ?? 'Hệ thống') . '</b> đã đăng một thông báo mới.',
                    'time' => $item->created_at,
                    'url' => url('/student/?filter=all#notification-' . $item->id),
                    'icon' => 'campaign',
                    'color' => 'text-emerald-600',
                    'bg' => 'bg-emerald-100',
                    'is_read' => in_array($alertId, $readAlerts),
                ];
            });
        $allAlerts = $allAlerts->merge($posts);

        // 2. THÔNG BÁO CÓ NGƯỜI TRẢ LỜI BÌNH LUẬN
        $replies = \App\Models\NotificationComment::with(['user', 'notification'])
            ->whereHas('parent', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('user_id', '!=', $userId)
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($item) use ($readAlerts) {
                $alertId = 'c_' . $item->id;
                return (object) [
                    'type' => 'comment',
                    'id' => $alertId,
                    'notification_id' => $item->notification_id,
                    'post_title' => $item->notification->title ?? 'Bài viết',
                    'message' =>
                        '<b>' .
                        $item->user->name .
                        '</b> đã phản hồi bình luận của bạn: "' .
                        \Illuminate\Support\Str::limit($item->content, 30) .
                        '"',
                    'time' => $item->created_at,
                    'url' => url('/student/?filter=all#comment-' . $item->id),
                    'icon' => 'reply',
                    'color' => 'text-blue-600',
                    'bg' => 'bg-blue-100',
                    'is_read' => in_array($alertId, $readAlerts),
                ];
            });
        $allAlerts = $allAlerts->merge($replies);
    }

    $allAlerts = $allAlerts->sortByDesc('time')->values();
    $unreadCount = $allAlerts->where('is_read', false)->count();
    $allAlertIds = $allAlerts->pluck('id')->values()->toJson();

    // Group cho tab "Theo bài viết"
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
    class="flex items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-6 sticky top-0 z-50 shadow-sm h-[56px]">
    {{-- LOGO & TITLE --}}
    <div class="flex items-center gap-4 sm:gap-6">
        <a href="{{ url('/student/dashboard') }}"
            class="flex items-center gap-2 text-primary hover:opacity-80 transition-opacity">
            <span class="material-symbols-outlined text-[26px]">school</span>
            <h2 class="text-[15px] font-extrabold leading-tight tracking-tight hidden sm:block font-display">Cổng Sinh
                Viên</h2>
        </a>

        {{-- SEARCH COMPACT --}}
        {{-- SEARCH COMPACT DÙNG ALPINE.JS --}}
        <div x-data="searchSystem()" @click.away="isOpen = false"
            class="relative hidden md:flex flex-col h-[36px] w-48 md:w-[400px] z-50">
            <div
                class="flex w-full flex-1 items-center rounded-sm bg-slate-100/80 border border-slate-200 focus-within:border-primary focus-within:bg-white focus-within:shadow-sm transition-all overflow-hidden px-3 relative">
                <span class="material-symbols-outlined !text-[18px] text-slate-400">search</span>
                <input x-model="searchQuery" @focus="if(searchQuery.length > 0) isOpen = true"
                    class="w-full bg-transparent text-slate-700 focus:outline-0 focus:ring-0 border-none px-2 text-[13px] placeholder:text-slate-400"
                    placeholder="Tìm kiếm tiêu đề, nội dung, file..." autocomplete="off" />

                {{-- Icon Loading --}}
                <span x-show="isLoading" x-cloak
                    class="material-symbols-outlined !text-[16px] animate-spin text-primary absolute right-3">progress_activity</span>

                {{-- Nút Xóa (Clear) --}}
                <button x-show="searchQuery.length > 0 && !isLoading" x-cloak
                    @click="searchQuery = ''; isOpen = false; $refs.searchInput.focus()"
                    class="absolute right-3 text-slate-400 hover:text-slate-600">
                    <span class="material-symbols-outlined !text-[16px]">close</span>
                </button>
            </div>

            {{-- POPUP KẾT QUẢ TÌM KIẾM --}}
            <div x-show="isOpen" x-transition x-cloak
                class="absolute top-[110%] left-0 w-[400px] bg-white rounded-sm shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-200 overflow-hidden flex flex-col max-h-[450px]">
                <div class="px-4 py-2 bg-slate-50/80 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="font-extrabold text-slate-800 text-[13px]">Kết quả tìm kiếm</h3>
                </div>

                <div class="overflow-y-auto custom-scrollbar flex-1 bg-white">
                    {{-- Trạng thái đang tìm kiếm --}}
                    <div x-show="isLoading" class="p-6 text-center text-slate-500">
                        <span
                            class="material-symbols-outlined !text-[24px] animate-spin block mb-2 opacity-50">progress_activity</span>
                        <p class="text-[12px]">Đang tìm kiếm...</p>
                    </div>

                    {{-- Không tìm thấy kết quả --}}
                    <div x-show="!isLoading && results.length === 0 && searchQuery.length > 0"
                        class="p-8 text-center text-slate-500">
                        <span class="material-symbols-outlined !text-[32px] block mb-2 opacity-40">search_off</span>
                        <p class="text-[13px]">Không tìm thấy kết quả nào cho "<span x-text="searchQuery"
                                class="font-bold text-slate-700"></span>"</p>
                    </div>

                    {{-- Danh sách kết quả --}}
                    <template x-for="item in results" :key="item.id">
                        <a :href="item.url" @click.prevent="handleResultClick(item.url)"
                            class="block p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors">
                            <h4 class="text-[13.5px] font-bold text-slate-800 leading-snug mb-1" x-text="item.title">
                            </h4>
                            <p class="text-[12px] text-slate-600 line-clamp-2 leading-relaxed" x-html="item.snippet">
                            </p>

                            {{-- Đính kèm file --}}
                            <template x-if="item.file">
                                <div
                                    class="mt-2 flex items-center gap-1 text-[11px] font-semibold text-primary bg-primary/10 w-max px-2 py-1 rounded-sm border border-primary/20">
                                    <span class="material-symbols-outlined !text-[14px]">description</span>
                                    <span class="truncate max-w-[250px]" x-text="item.file"></span>
                                </div>
                            </template>

                            <div class="mt-2 flex items-center justify-between text-[11px] font-medium text-slate-500">
                                <span class="flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-[13px]">person</span> <span
                                        x-text="item.sender"></span>
                                </span>
                                <span x-text="item.time"></span>
                            </div>
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- RIGHT ACTIONS --}}
    <div class="flex items-center gap-1 sm:gap-4">
        {{-- DROPDOWN THÔNG BÁO (ALPINE.JS) --}}
        <div x-data="{
            alertOpen: false,
            unread: {{ $unreadCount }},
            tab: 'all',
            expandedPost: null,
        
            handleAlertClick(alertId, targetUrl) {
                let rowAll = document.getElementById('alert-row-' + alertId);
                let rowGroup = document.getElementById('alert-row-' + alertId + '-group');
                let isRead = (rowAll && rowAll.getAttribute('data-is-read') === 'true');
        
                let goToTarget = () => {
                    this.alertOpen = false;
        
                    let targetObj = new URL(targetUrl, window.location.origin);
                    let currentPath = window.location.pathname.replace(/\/$/, '');
                    let targetPath = targetObj.pathname.replace(/\/$/, '');
        
                    let currentSearch = window.location.search || '?filter=all';
                    let targetSearch = targetObj.search || '?filter=all';
        
                    if (currentPath === targetPath && currentSearch === targetSearch) {
                        if (window.history.pushState) {
                            window.history.pushState(null, null, targetObj.search + targetObj.hash);
                        } else {
                            window.location.hash = targetObj.hash;
                        }
        
                        if (targetObj.hash && typeof window.highlightCommentBubble === 'function') {
                            window.highlightCommentBubble(targetObj.hash);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.location.href = targetUrl;
                    }
                };
        
                if (!isRead) {
                    if (rowAll) {
                        rowAll.setAttribute('data-is-read', 'true');
                        rowAll.classList.remove('bg-blue-50/30', 'alert-unread-bg');
                        let dot = rowAll.querySelector('.unread-dot');
                        if (dot) dot.remove();
                    }
                    if (rowGroup) {
                        rowGroup.setAttribute('data-is-read', 'true');
                        rowGroup.classList.remove('bg-blue-50/30', 'alert-unread-bg');
                    }
                    if (this.unread > 0) this.unread--;
        
                    fetch('{{ url('/student/alerts/mark-read') }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ alert_id: alertId })
                        })
                        .then(() => goToTarget())
                        .catch(() => goToTarget());
                } else {
                    goToTarget();
                }
            }
        }" @click.away="alertOpen = false" class="relative flex items-center">

            {{-- Nút Chuông --}}
            <button @click="alertOpen = !alertOpen"
                class="relative p-2 text-slate-500 hover:text-primary hover:bg-slate-50 rounded-full transition-colors focus:outline-none">
                <span class="material-symbols-outlined !text-[24px]">notifications</span>
                <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" x-transition
                    class="absolute top-1 right-1 min-w-[16px] h-[16px] px-[4px] rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center ring-2 ring-white"
                    x-cloak>
                </span>
            </button>

            {{-- BẢNG DROPDOWN --}}
            <div x-show="alertOpen" x-transition
                class="absolute right-0 top-full mt-3 w-[340px] sm:w-[400px] bg-white rounded-sm shadow-[0_10px_40px_-10px_rgba(0,0,0,0.15)] border border-slate-200 overflow-hidden flex flex-col z-50"
                x-cloak>

                <div class="px-5 py-3 flex justify-between items-center bg-slate-50/80 border-b border-slate-100">
                    <h3 class="font-extrabold text-slate-800 text-[15px]">Thông báo</h3>
                    <span class="text-[12px] text-primary font-bold cursor-pointer hover:underline"
                        @click="
                            if(unread > 0) {
                                fetch('{{ url('/student/alerts/mark-read-all') }}', { 
                                    method: 'POST', 
                                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                                    body: JSON.stringify({ alert_ids: {{ $allAlertIds }} })
                                }).then(() => {
                                    unread = 0;
                                    document.querySelectorAll('.alert-unread-bg').forEach(el => {
                                        el.classList.remove('bg-blue-50/30', 'alert-unread-bg');
                                        el.setAttribute('data-is-read', 'true');
                                    });
                                    document.querySelectorAll('.unread-dot, .unread-badge').forEach(el => el.remove());
                                });
                            }">Đánh
                        dấu đã đọc</span>
                </div>

                <div class="flex border-b border-slate-100 px-2 bg-white">
                    <button @click.prevent="tab = 'all'"
                        :class="tab === 'all' ? 'border-primary text-primary font-bold' :
                            'border-transparent text-slate-500 font-semibold hover:bg-slate-50'"
                        class="flex-1 py-2.5 text-[13px] border-b-2 transition-colors rounded-t-lg">Tất cả</button>
                    <button @click.prevent="tab = 'group'"
                        :class="tab === 'group' ? 'border-primary text-primary font-bold' :
                            'border-transparent text-slate-500 font-semibold hover:bg-slate-50'"
                        class="flex-1 py-2.5 text-[13px] border-b-2 transition-colors rounded-t-lg">Theo bài
                        viết</button>
                </div>

                <div class="max-h-[420px] overflow-y-auto overscroll-contain custom-scrollbar bg-white">
                    {{-- TAB 1 --}}
                    <div x-show="tab === 'all'" class="flex flex-col">
                        @forelse ($allAlerts as $alert)
                            <a href="{{ $alert->url }}" id="alert-row-{{ $alert->id }}"
                                data-is-read="{{ $alert->is_read ? 'true' : 'false' }}"
                                @click.prevent="handleAlertClick('{{ $alert->id }}', '{{ $alert->url }}')"
                                class="alert-unread-bg flex items-start gap-3 p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/30' : '' }}">

                                @if (!$alert->is_read)
                                    <div
                                        class="unread-dot absolute left-2 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-600 rounded-full">
                                    </div>
                                @endif

                                <div
                                    class="w-10 h-10 ml-2 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm {{ $alert->bg }} {{ $alert->color }}">
                                    <span class="material-symbols-outlined !text-[20px]">{{ $alert->icon }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[13.5px] text-slate-700 leading-snug">{!! $alert->message !!}</p>
                                    <p class="text-[11px] font-semibold text-primary mt-1.5 flex items-center gap-1">
                                        {{ $alert->time->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-10 text-center text-slate-400">
                                <span
                                    class="material-symbols-outlined !text-[48px] opacity-40 mb-3 block">notifications_paused</span>
                                <p class="text-[13px] font-medium">Bạn không có thông báo mới.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- TAB 2 --}}
                    <div x-show="tab === 'group'" style="display: none;" class="flex flex-col">
                        @forelse ($groupedAlerts as $group)
                            <div class="border-b border-slate-100">
                                <div @click.prevent="expandedPost = expandedPost === {{ $group->notification_id }} ? null : {{ $group->notification_id }}"
                                    class="flex justify-between items-center p-4 cursor-pointer hover:bg-slate-50 transition-colors">
                                    <div class="flex-1 min-w-0 pr-3">
                                        <h4 class="text-[13.5px] font-bold text-slate-800 truncate">
                                            <span
                                                class="material-symbols-outlined !text-[16px] text-slate-400 align-middle mr-1">feed</span>
                                            {{ $group->post_title }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 mt-1 font-medium">
                                            {{ $group->items->count() }} hoạt động</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($group->unread_count > 0)
                                            <span
                                                class="unread-badge bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full shadow-sm">{{ $group->unread_count }}
                                                mới</span>
                                        @endif
                                        <span
                                            class="material-symbols-outlined !text-[20px] text-slate-400 transition-transform duration-200"
                                            :class="expandedPost === {{ $group->notification_id }} ? 'rotate-180' : ''">expand_more</span>
                                    </div>
                                </div>
                                <div x-show="expandedPost === {{ $group->notification_id }}" x-collapse
                                    class="bg-slate-50/50 border-t border-slate-100">
                                    @foreach ($group->items as $alert)
                                        <a href="{{ $alert->url }}" id="alert-row-{{ $alert->id }}-group"
                                            data-is-read="{{ $alert->is_read ? 'true' : 'false' }}"
                                            @click.prevent="handleAlertClick('{{ $alert->id }}', '{{ $alert->url }}')"
                                            class="alert-unread-bg flex items-start gap-3 py-3 px-4 pl-8 border-b border-slate-50 hover:bg-slate-100 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/40' : '' }}">

                                            <div
                                                class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 shadow-sm {{ $alert->bg }} {{ $alert->color }}">
                                                <span
                                                    class="material-symbols-outlined !text-[16px]">{{ $alert->icon }}</span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-[12.5px] text-slate-700 leading-snug">
                                                    {!! $alert->message !!}</p>
                                                <p class="text-[10px] font-semibold text-slate-500 mt-1">
                                                    {{ $alert->time->diffForHumans() }}</p>
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="p-10 text-center text-slate-400">
                                <p class="text-[13px] font-medium">Chưa có bài viết nào được tương tác.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="w-px h-6 bg-slate-200 mx-2 hidden md:block"></div>

        <div class="flex items-center gap-3">
            <div class="hidden sm:block text-right">
                <p class="text-[13px] font-bold text-slate-800 leading-none">{{ Auth::user()->name }}</p>
                <p class="text-[11px] text-slate-500 mt-0.5">Sinh viên</p>
            </div>
            <div
                class="h-9 w-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-[14px] shadow-sm border border-primary/20">
                {{ mb_substr(Auth::user()->name ?? 'S', 0, 1) }}
            </div>
            <form method="POST" action="{{ url('/logout') }}" class="m-0 ml-1">
                @csrf
                <button type="submit"
                    class="flex items-center justify-center p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-full transition-colors"
                    title="Đăng xuất">
                    <span class="material-symbols-outlined !text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</header>
