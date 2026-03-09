@php
    $user = Auth::user();
    $userId = $user ? $user->id : 0;
    $student = $user->student ?? null;
    $classId = $student ? $student->class_id : null;

    $readAlerts = \DB::table('user_read_alerts')->where('user_id', $userId)->pluck('alert_id')->toArray();
    $allAlerts = collect();

    if ($user) {
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

    $currentFilter = request()->get('filter', 'all');
    $currentTimeFilter = request()->get('time', 'all');
@endphp

<header class="bg-white border-b border-slate-200 sticky top-0 z-40 w-full flex flex-col shadow-sm"
    x-data="{ showMobileSearch: false }">
    <div class="flex items-center justify-between px-3 lg:px-6 h-[60px] w-full relative">

        {{-- BÊN TRÁI: Toggle Sidebar & Logo --}}
        <div class="flex items-center flex-1 gap-2 sm:gap-4">
            <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden p-2 text-slate-600 hover:bg-slate-100 rounded-full transition-colors">
                <span class="material-symbols-outlined !text-[24px]">menu</span>
            </button>
            {{-- Ở GIỮA: Thanh Tìm Kiếm Desktop --}}
            <div class="flex-1 max-w-[600px] rounded-sm hidden md:block relative ml-4" x-data="liveSearch()"
                @click.away="showPopup = false">
                <form action="{{ url('/student') }}" method="GET"
                    class="flex w-full items-center rounded-sm bg-slate-100/80 border border-slate-200 focus-within:border-primary focus-within:bg-white focus-within:shadow-sm transition-all overflow-hidden px-3 h-[40px]">
                    @if (request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    @if (request('time'))
                        <input type="hidden" name="time" value="{{ request('time') }}">
                    @endif

                    <span class="material-symbols-outlined !text-[18px] text-slate-400">search</span>
                    <input type="text" name="search" x-model="query" @input.debounce.300ms="fetchData"
                        class="w-full rounded-sm bg-transparent text-slate-700 focus:outline-0 focus:ring-0 border-none px-2 text-[14px] placeholder:text-slate-400"
                        placeholder="Tìm thông báo, file đính kèm..." autocomplete="off"
                        @focus="if(query.trim().length > 0) showPopup = true" />

                    <span x-show="loading"
                        class="material-symbols-outlined !text-[16px] text-primary animate-spin absolute right-3"
                        x-cloak>sync</span>
                    <button type="button" x-show="query.length > 0 && !loading"
                        @click="query = ''; showPopup = false; window.location.href='{{ url('/student?filter=' . $currentFilter . '&time=' . $currentTimeFilter) }}'"
                        class="text-slate-400 hover:text-red-500 flex items-center absolute right-3" x-cloak>
                        <span class="material-symbols-outlined !text-[16px]">close</span>
                    </button>
                </form>

                {{-- Popup Search Desktop --}}
                <div x-show="showPopup" x-transition.opacity.duration.200ms
                    class="absolute left-0 w-full md:w-[600px] top-full mt-2 bg-white rounded-sm shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-50"
                    x-cloak>
                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100"><span
                            class="text-[12px] font-bold text-slate-500 uppercase">Kết quả tìm kiếm</span></div>
                    <div class="max-h-[400px] overflow-y-auto custom-scrollbar flex flex-col">
                        <div x-show="loading" class="p-6 text-center text-slate-400"><span
                                class="material-symbols-outlined !text-[32px] animate-spin block mb-2">autorenew</span>
                            <p class="text-[13px] font-medium">Đang tìm kiếm...</p>
                        </div>
                        <div x-show="!loading && results.length === 0" class="p-6 text-center text-slate-400"><span
                                class="material-symbols-outlined !text-[40px] opacity-50 mb-2 block">search_off</span>
                            <p class="text-[13px] font-medium">Không tìm thấy thông báo nào.</p>
                        </div>
                        <template x-for="item in results" :key="item.id">
                            <a href="#" @click.prevent="goToPost(item.url)"
                                class="flex items-start gap-3 p-3.5 border-b border-slate-50 hover:bg-blue-50/40 transition-colors">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 bg-slate-100 text-slate-500 mt-0.5">
                                    <span class="material-symbols-outlined !text-[20px]">article</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[13.5px] font-bold text-slate-800 leading-tight mb-1"
                                        x-text="item.title"></h4>
                                    <p class="text-[12.5px] text-slate-500 leading-snug line-clamp-2"
                                        x-text="item.snippet"></p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        {{-- BÊN PHẢI: Search Mobile, Chuông, Profile Hover --}}
        <div class="flex items-center justify-end gap-1 sm:gap-3 flex-shrink-0">
            <button @click="showMobileSearch = true"
                class="md:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-colors focus:outline-none">
                <span class="material-symbols-outlined !text-[24px]">search</span>
            </button>

            {{-- CHUÔNG THÔNG BÁO --}}
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
                        if (window.location.pathname === targetObj.pathname) {
                            if (window.history.pushState) window.history.pushState(null, null, targetObj.search + targetObj.hash);
                            else window.location.hash = targetObj.hash;
                            if (targetObj.hash && typeof window.highlightCommentBubble === 'function') window.highlightCommentBubble(targetObj.hash);
                            else window.location.reload();
                        } else { window.location.href = targetUrl; }
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
                        fetch('{{ url('/student/alerts/mark-read') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, body: JSON.stringify({ alert_id: alertId }) }).then(() => goToTarget()).catch(() => goToTarget());
                    } else { goToTarget(); }
                }
            }" @click.away="alertOpen = false" class="relative mr-1">
                <button @click="alertOpen = !alertOpen"
                    class="relative p-2 text-slate-500 hover:text-primary hover:bg-slate-100 rounded-full transition-colors focus:outline-none">
                    <span class="material-symbols-outlined !text-[24px]">notifications</span>
                    <span x-show="unread > 0" x-text="unread > 9 ? '9+' : unread" x-transition
                        class="absolute top-1.5 right-1 min-w-[16px] h-[16px] px-[3px] rounded-full bg-red-500 text-white text-[9px] font-bold flex items-center justify-center ring-[1.5px] ring-white"
                        x-cloak></span>
                </button>

                <div x-show="alertOpen" x-transition
                    class="fixed left-2 right-2 top-[65px] sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 sm:w-[420px] bg-white rounded-sm shadow-2xl border border-slate-200 overflow-hidden flex flex-col z-[80] sm:origin-top-right"
                    x-cloak>
                    <div class="px-5 py-3.5 flex justify-between items-center bg-slate-50/80 border-b border-slate-100">
                        <h3 class="font-extrabold text-slate-800 text-[15px]">Thông báo</h3>
                        <span class="text-xs text-primary font-semibold cursor-pointer hover:underline"
                            @click="if(unread > 0) { fetch('{{ url('/student/alerts/mark-read-all') }}', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, body: JSON.stringify({ alert_ids: {{ $allAlertIds }} }) }).then(() => { unread = 0; document.querySelectorAll('.alert-unread-bg').forEach(el => { el.classList.remove('bg-blue-50/30', 'alert-unread-bg'); el.setAttribute('data-is-read', 'true'); }); document.querySelectorAll('.unread-dot, .unread-badge').forEach(el => el.remove()); }); }">Đã
                            đọc tất cả</span>
                    </div>
                    <div class="flex border-b border-slate-200 px-2 bg-white">
                        <button @click.prevent="tab = 'all'"
                            :class="tab === 'all' ? 'border-primary text-primary font-bold' :
                                'border-transparent text-slate-500 font-medium'"
                            class="flex-1 py-3 text-[13px] border-b-2 transition-colors">Tất cả thông báo</button>
                        <button @click.prevent="tab = 'group'"
                            :class="tab === 'group' ? 'border-primary text-primary font-bold' :
                                'border-transparent text-slate-500 font-medium'"
                            class="flex-1 py-3 text-[13px] border-b-2 transition-colors">Theo bài viết</button>
                    </div>
                    <div
                        class="max-h-[60vh] sm:max-h-[450px] overflow-y-auto overscroll-contain custom-scrollbar bg-white">
                        <div x-show="tab === 'all'" class="flex flex-col">
                            @forelse ($allAlerts as $alert)
                                <a href="{{ $alert->url }}" id="alert-row-{{ $alert->id }}"
                                    data-is-read="{{ $alert->is_read ? 'true' : 'false' }}"
                                    @click.prevent="handleAlertClick('{{ $alert->id }}', '{{ $alert->url }}')"
                                    class="alert-unread-bg flex items-start gap-3 sm:gap-4 p-3 sm:p-4 border-b border-slate-50 hover:bg-slate-50 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/30' : '' }}">
                                    @if (!$alert->is_read)
                                        <div
                                            class="unread-dot absolute left-1.5 top-1/2 -translate-y-1/2 w-2 h-2 bg-blue-500 rounded-full">
                                        </div>
                                    @endif
                                    <div
                                        class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 {{ $alert->bg }} {{ $alert->color }}">
                                        <span
                                            class="material-symbols-outlined text-[18px] sm:text-[22px]">{{ $alert->icon }}</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[13px] text-slate-700 leading-snug">{!! $alert->message !!}</p>
                                        <p class="text-[11px] font-medium text-blue-600 mt-1.5 flex items-center gap-1">
                                            <span
                                                class="material-symbols-outlined !text-[13px]">schedule</span>{{ $alert->time->diffForHumans() }}
                                        </p>
                                    </div>
                                </a>
                            @empty
                                <div class="p-8 text-center text-slate-400">
                                    <p class="text-sm font-medium">Bạn không có thông báo mới.</p>
                                </div>
                            @endforelse
                        </div>
                        <div x-show="tab === 'group'" style="display: none;"
                            class="flex flex-col bg-slate-50 border-b border-slate-200">
                            @forelse ($groupedAlerts as $group)
                                <div class="border-b border-slate-200/60 bg-white">
                                    <div @click.prevent="expandedPost = expandedPost === {{ $group->notification_id }} ? null : {{ $group->notification_id }}"
                                        class="flex justify-between items-center p-4 cursor-pointer hover:bg-slate-50 transition-colors">
                                        <div class="flex-1 min-w-0 pr-3">
                                            <h4 class="text-[13px] font-bold text-slate-800 truncate"><span
                                                    class="material-symbols-outlined !text-[16px] text-slate-400 align-middle mr-1">feed</span>{{ $group->post_title ?? 'Bài viết không xác định' }}
                                            </h4>
                                            <p class="text-[11px] text-slate-500 mt-1">{{ $group->items->count() }}
                                                hoạt động gần đây</p>
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
                                                class="alert-unread-bg flex items-start gap-3 py-3 px-4 pl-8 border-b border-slate-100 hover:bg-slate-100 transition-colors relative {{ !$alert->is_read ? 'bg-blue-50/30' : '' }}">
                                                @if (!$alert->is_read)
                                                    <div
                                                        class="unread-dot absolute left-1.5 top-1/2 -translate-y-1/2 w-1.5 h-1.5 bg-blue-500 rounded-full">
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

            {{-- AVATAR VÀ MENU ĐĂNG XUẤT --}}
            <div class="relative group" x-data="{ profileOpen: false }" @click.away="profileOpen = false">
                <button @click="profileOpen = !profileOpen"
                    class="flex items-center gap-2 bg-slate-50 border border-slate-200 p-1 sm:pr-3 rounded-full hover:bg-slate-100 transition-colors focus:outline-none">
                    <div
                        class="h-8 w-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-[13px] shadow-sm">
                        {{ mb_substr(Auth::user()->name ?? 'S', 0, 1) }}
                    </div>
                    <div class="hidden sm:block text-left max-w-[120px]">
                        <p class="text-[12px] font-bold text-slate-800 leading-none truncate">{{ Auth::user()->name }}
                        </p>
                    </div>
                    <span
                        class="material-symbols-outlined !text-[16px] text-slate-400 hidden sm:block">expand_more</span>
                </button>

                <div :class="profileOpen ? 'opacity-100 visible translate-y-0' : 'opacity-0 invisible translate-y-2'"
                    class="sm:group-hover:opacity-100 sm:group-hover:visible sm:group-hover:translate-y-0 absolute right-0 top-full mt-2 w-48 bg-white rounded-sm shadow-lg border border-slate-100 transition-all duration-200 z-50">
                    <div class="p-3 border-b border-slate-50 sm:hidden">
                        <p class="text-[13px] font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-500">Sinh viên</p>
                    </div>
                    <form method="POST" action="{{ url('/logout') }}" class="m-0 p-1">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center gap-2 px-3 py-2 text-[13px] font-bold text-red-600 hover:bg-red-50 rounded-sm transition-colors text-left">
                            <span class="material-symbols-outlined !text-[18px]">logout</span>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    {{-- KHUNG SEARCH FULL MÀN HÌNH DÀNH CHO MOBILE --}}
    <div x-show="showMobileSearch" x-transition.opacity
        class="absolute inset-0 z-[60] bg-white h-[60px] flex items-center px-4 w-full md:hidden shadow-sm" x-cloak>
        <div class="w-full flex items-center gap-2" x-data="liveSearch()" @click.away="showPopup = false">
            <form action="{{ url('/student') }}" method="GET"
                class="flex-1 flex items-center bg-slate-100 rounded-full px-3 h-[40px] focus-within:ring-1 focus-within:ring-primary relative">
                @if (request('filter'))
                    <input type="hidden" name="filter" value="{{ request('filter') }}">
                @endif
                @if (request('time'))
                    <input type="hidden" name="time" value="{{ request('time') }}">
                @endif

                <span class="material-symbols-outlined !text-[18px] text-slate-400">search</span>
                <input type="text" name="search" x-model="query" @input.debounce.300ms="fetchData"
                    @focus="if(query.trim().length > 0) showPopup = true"
                    class="w-full bg-transparent text-slate-700 border-none px-2 focus:ring-0 text-[14px] placeholder:text-slate-400"
                    placeholder="Tìm kiếm..." autofocus />
                <span x-show="loading"
                    class="material-symbols-outlined !text-[16px] text-primary animate-spin absolute right-3"
                    x-cloak>sync</span>

                <div x-show="showPopup" x-transition.opacity.duration.200ms
                    class="absolute left-0 right-0 top-full mt-2 w-[100vw] -ml-4 bg-white shadow-xl border-t border-slate-200 flex flex-col z-[70] h-[calc(100vh-60px)]"
                    x-cloak>
                    <div class="px-4 py-2.5 bg-slate-50 border-b border-slate-100"><span
                            class="text-[12px] font-bold text-slate-500 uppercase">Kết quả</span></div>
                    <div class="overflow-y-auto pb-20 custom-scrollbar flex flex-col">
                        <div x-show="loading" class="p-8 text-center text-slate-400">
                            <p>Đang tìm...</p>
                        </div>
                        <div x-show="!loading && results.length === 0" class="p-8 text-center text-slate-400">
                            <p>Không tìm thấy.</p>
                        </div>
                        <template x-for="item in results" :key="item.id">
                            <a href="#" @click.prevent="goToPost(item.url); showMobileSearch = false"
                                class="flex items-start gap-3 p-4 border-b border-slate-50 hover:bg-blue-50/40">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center bg-slate-100 text-slate-500">
                                    <span class="material-symbols-outlined !text-[20px]">article</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-[14px] font-bold text-slate-800 truncate" x-text="item.title">
                                    </h4>
                                    <p class="text-[12px] text-slate-500 line-clamp-2 mt-0.5" x-text="item.snippet">
                                    </p>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </form>
            <button type="button" @click="showMobileSearch = false; query=''"
                class="p-2 text-slate-500 font-semibold text-[14px]">Hủy</button>
        </div>
    </div>
</header>
