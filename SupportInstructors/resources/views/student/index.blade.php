@extends('layouts.student')
@section('title', 'Bảng tin')

@section('styles')
    <style>
        .target-comment {
            animation: highlightComment 2.5s ease-out forwards;
            border-radius: 0.5rem;
        }

        @keyframes highlightComment {
            0% {
                background-color: #fff7ed;
            }

            100% {
                background-color: transparent;
            }
        }

        /* Tweak nhỏ giống style admin, dùng rounded-sm */
        .post-card {
            border-radius: 0.25rem;
            /* rounded-sm */
        }

        .post-card .post-header h3,
        .post-card .post-header p {
            line-height: 1.05;
        }

        /* Giảm độ nổi của shadow cho bảng tin */
        .post-card.shadow-sm {
            box-shadow: 0 1px 4px rgba(2, 6, 23, 0.04), 0 1px 2px rgba(2, 6, 23, 0.04);
        }

        /* avatar image fit */
        .avatar-img {
            object-fit: cover;
            display: block;
        }
    </style>
@endsection


@section('content')
    @php
        $student = Auth::user()->student;
        $class = $student ? $student->class : null;
        $currentFilter = $filter ?? 'all';
    @endphp

    <section class="col-span-12 md:col-span-8 lg:col-span-9 flex flex-col gap-4">

        {{-- LỌC --}}
        <div
            class="bg-white rounded-sm shadow-sm border border-slate-200 p-3 flex flex-wrap items-center justify-between gap-3 sticky top-[72px] z-40">
            <h2 class="text-[14px] font-bold text-slate-800 flex items-center gap-1.5 font-display">
                <span class="material-symbols-outlined !text-[18px] text-primary">feed</span> Bảng tin
            </h2>
            <div class="flex gap-1.5">
                <a href="{{ url('/?filter=all') }}"
                    class="px-3 py-1.5 text-[11px] font-semibold rounded-sm transition-colors {{ $currentFilter == 'all' ? 'bg-primary text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Tất
                    cả</a>
                <a href="{{ url('/?filter=urgent') }}"
                    class="px-3 py-1.5 text-[11px] font-semibold rounded-sm transition-colors flex items-center gap-1 {{ $currentFilter == 'urgent' ? 'bg-red-500 text-white' : 'bg-red-50 text-red-600 hover:bg-red-100' }}">
                    <span class="material-symbols-outlined !text-[12px]">error</span> Khẩn cấp
                </a>
                <a href="{{ url('/?filter=warning') }}"
                    class="px-3 py-1.5 text-[11px] font-semibold rounded-sm transition-colors flex items-center gap-1 {{ $currentFilter == 'warning' ? 'bg-orange-500 text-white' : 'bg-orange-50 text-orange-600 hover:bg-orange-100' }}">
                    <span class="material-symbols-outlined !text-[12px]">warning</span> Chú ý
                </a>
            </div>
        </div>

        {{-- BÀI VIẾT --}}
        @forelse($notifications as $notify)
            <article id="notification-{{ $notify->id }}" data-notification-id="{{ $notify->id }}"
                x-data="{ showComments: false }"
                class="post-card bg-white rounded-sm shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 sm:p-5">
                    <div class="flex justify-between items-start mb-3 post-header">
                        <div class="flex items-center gap-3">
                            <div
                                class="h-10 w-10 rounded-sm text-white flex items-center justify-center font-bold text-[14px] shadow-sm {{ ($notify->sender->role_id ?? 0) == 1 ? 'bg-blue-600' : 'bg-emerald-600' }}">
                                {{ mb_substr($notify->sender->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h3
                                    class="font-bold text-[14px] text-slate-900 dark:text-slate-100 hover:underline cursor-pointer font-display">
                                    {{ $notify->sender->name ?? 'Hệ thống' }}</h3>
                                <p class="text-[12px] text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                                    {{ $notify->created_at->diffForHumans() }} <span>•</span>
                                    @if ($notify->target_audience == 'all')
                                        <span class="flex items-center gap-0.5 text-blue-600 font-medium"><span
                                                class="material-symbols-outlined !text-[12px]">public</span> Toàn
                                            trường</span>
                                    @else
                                        <span class="flex items-center gap-0.5 text-emerald-600 font-medium"><span
                                                class="material-symbols-outlined !text-[12px]">group</span> Lớp
                                            {{ $class->code ?? '' }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($notify->type == 'urgent')
                            <span
                                class="bg-red-50 text-red-700 border border-red-100 text-[10px] font-bold px-2 py-0.5 rounded-sm uppercase flex items-center gap-1"><span
                                    class="material-symbols-outlined !text-[12px]">error</span> Khẩn cấp</span>
                        @elseif($notify->type == 'warning')
                            <span
                                class="bg-orange-50 text-orange-700 border border-orange-100 text-[10px] font-bold px-2 py-0.5 rounded-sm uppercase flex items-center gap-1"><span
                                    class="material-symbols-outlined !text-[12px]">warning</span> Chú ý</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <h4
                            class="font-bold text-[15px] mb-1.5 text-slate-900 dark:text-slate-100 font-display leading-snug">
                            {{ $notify->title }}</h4>
                        <div class="prose prose-slate max-w-none text-[14px] text-slate-700 dark:text-slate-300 leading-relaxed prose-a:text-blue-600 hover:prose-a:underline"
                            style="word-wrap: break-word;">
                            {!! $notify->message !!}
                        </div>
                    </div>

                    @if ($notify->attachment_url)
                        <a href="{{ asset('storage/' . $notify->attachment_url) }}"
                            download="{{ $notify->attachment_name }}"
                            class="bg-slate-50 rounded-sm p-3 mb-3 flex items-center gap-3 border border-slate-200 cursor-pointer hover:bg-slate-100 transition-colors group">
                            <div
                                class="bg-white border border-slate-200 text-primary p-2 rounded-sm shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined !text-[18px]">description</span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-[13px] font-bold text-slate-800 truncate">{{ $notify->attachment_name }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Nhấn tải xuống tài liệu</p>
                            </div>
                            <div class="text-slate-400 group-hover:text-primary p-1"><span
                                    class="material-symbols-outlined !text-[16px]">download</span></div>
                        </a>
                    @endif

                    <div
                        class="flex items-center justify-between text-[12px] font-medium text-slate-500 pb-2 border-b border-slate-100">
                        <span class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-50 text-blue-600">
                                <span class="material-symbols-outlined !text-[14px]">thumb_up</span>
                            </span>
                            <span class="likes-count-{{ $notify->id }} text-slate-700">{{ $notify->likes_count }}</span>
                            <span class="text-slate-400 text-[12px]">Lượt thích</span>
                        </span>
                        <span class="flex items-center gap-1.5 hover:underline cursor-pointer"
                            @click="showComments = !showComments">{{ $notify->comments_count }} Bình luận</span>
                    </div>

                    <div class="flex gap-2 pt-3">
                        <form action="{{ url('student/notifications/' . $notify->id . '/like') }}" method="POST"
                            class="flex-1 js-ajax-like">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-2 text-[13px] font-semibold rounded-sm transition-colors {{ $notify->isLikedBy(Auth::id()) ? 'text-primary bg-primary/5' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span class="material-symbols-outlined !text-[16px] transition-transform active:scale-125"
                                    {{ $notify->isLikedBy(Auth::id()) ? 'style=font-variation-settings:"FILL"1' : '' }}>thumb_up</span>
                                Thích
                            </button>
                        </form>
                        <button @click="showComments = !showComments"
                            class="flex-1 flex items-center justify-center gap-2 py-2 text-[13px] font-semibold text-slate-600 hover:bg-slate-50 rounded-sm transition-colors">
                            <span class="material-symbols-outlined !text-[16px]">chat_bubble_outline</span> Bình luận
                        </button>
                    </div>
                </div>

                {{-- KHU VỰC BÌNH LUẬN --}}
                <div x-show="showComments" x-transition class="border-t border-slate-100 bg-slate-50/50 p-3 sm:p-4" x-cloak>
                    <div class="space-y-4 mb-3 max-h-[350px] overflow-y-auto hide-scroll p-1"
                        id="comments-list-{{ $notify->id }}">

                        @forelse($notify->comments as $comment)
                            @php $replyCount = isset($comment->replies) ? $comment->replies->count() : 0; @endphp

                            <div id="comment-{{ $comment->id }}" class="relative" x-data="{ openReply: false, showAllReplies: false, replyToName: '' }">
                                <div class="flex gap-2 items-start">
                                    <div
                                        class="w-8 h-8 rounded-sm flex items-center justify-center text-white font-bold text-[12px] shrink-0 shadow-sm {{ $comment->user->role_id == 3 ? 'bg-slate-600' : ($comment->user->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600') }}">
                                        {{ mb_substr($comment->user->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0 text-left">
                                        <div
                                            class="bg-white border border-slate-200/80 px-3 py-2 rounded-sm inline-block max-w-full shadow-sm text-left">
                                            <div class="flex items-center gap-1.5 mb-0.5">
                                                <h5
                                                    class="text-[13px] font-bold text-slate-800 hover:underline cursor-pointer">
                                                    {{ $comment->user->name }}</h5>
                                                @if ($comment->user->role_id == 1)
                                                    <span class="material-symbols-outlined text-blue-500 !text-[13px]"
                                                        title="Admin">verified</span>
                                                @elseif($comment->user->role_id == 2)
                                                    <span class="material-symbols-outlined text-emerald-500 !text-[13px]"
                                                        title="Giảng viên/Cố vấn">school</span>
                                                @endif
                                            </div>
                                            <div class="text-[13px] text-slate-700 leading-snug w-full break-words">
                                                {!! nl2br(e(trim($comment->content))) !!}</div>
                                        </div>
                                        <div
                                            class="flex items-center gap-3 text-[11px] text-slate-500 mt-1 ml-1.5 font-medium">
                                            <button
                                                @click="openReply = !openReply; replyToName='{{ $comment->user->name }}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })"
                                                class="hover:underline hover:text-slate-900 transition-colors">Phản
                                                hồi</button>
                                            <span>·</span>
                                            <span>{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        {{-- Replies --}}
                                        @if ($replyCount > 0)
                                            <div
                                                class="replies-wrap ml-6 mt-3 flex flex-col gap-3 relative before:content-[''] before:absolute before:-left-[1rem] before:top-0 before:bottom-0 before:w-[2px] before:bg-slate-200 before:rounded-full">

                                                @if ($replyCount > 1)
                                                    <button @click="showAllReplies = !showAllReplies"
                                                        class="inline-flex items-center gap-1.5 text-slate-500 hover:text-slate-800 font-bold text-[11px] py-1 transition-colors bg-white pr-2 rounded-sm w-max">
                                                        <span class="material-symbols-outlined !text-[14px] text-slate-400"
                                                            x-text="showAllReplies ? 'subdirectory_arrow_left' : 'subdirectory_arrow_right'"></span>
                                                        <span
                                                            x-text="showAllReplies ? 'Ẩn bớt phản hồi' : 'Xem {{ $replyCount - 1 }} phản hồi trước'"></span>
                                                    </button>
                                                @endif

                                                @foreach ($comment->replies as $index => $reply)
                                                    <div id="comment-{{ $reply->id }}"
                                                        class="bg-white border border-slate-100 p-2 rounded-sm shadow-sm w-full break-words"
                                                        @if ($replyCount > 1 && $index < $replyCount - 1) x-show="showAllReplies" x-transition @endif>
                                                        @if (!empty($reply->parent) && !empty($reply->parent->user))
                                                            <div
                                                                class="inline-block text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-[1px] rounded mb-1 font-bold border border-indigo-100">
                                                                Trả lời: {{ $reply->parent->user->name }}</div>
                                                        @endif
                                                        <div class="flex items-start gap-2">
                                                            <div
                                                                class="w-6 h-6 flex-none flex items-center justify-center rounded-sm text-white font-bold text-[10px] shadow-sm {{ $reply->user->role_id == 3 ? 'bg-slate-600' : ($reply->user->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600') }}">
                                                                {{ mb_substr($reply->user->name, 0, 1) }}
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <div class="font-bold text-[12px] text-slate-900">
                                                                    {{ $reply->user->name }}</div>
                                                                <div
                                                                    class="text-[13px] text-slate-800 mt-0.5 leading-snug break-words w-full">
                                                                    {!! preg_replace(
                                                                        '/(@[^\s:]+:?)/',
                                                                        '<strong class="text-indigo-600">$1</strong>',
                                                                        nl2br(e(trim($reply->content))),
                                                                    ) !!}
                                                                </div>
                                                                <div
                                                                    class="mt-1 flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                                                    <button
                                                                        @click="openReply = true; showAllReplies = true; replyToName='{{ $reply->user->name }}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })"
                                                                        class="hover:underline hover:text-slate-900 transition-colors">Phản
                                                                        hồi</button>
                                                                    <span>·</span>
                                                                    <span>{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Khối Replies mồi --}}
                                        @if ($replyCount == 0)
                                            <div
                                                class="replies-wrap ml-6 mt-3 flex flex-col gap-3 relative before:content-[''] before:absolute before:-left-[1rem] before:top-0 before:bottom-0 before:w-[2px] before:bg-slate-200 before:rounded-full empty:hidden">
                                            </div>
                                        @endif

                                        {{-- Form Phản hồi --}}
                                        <div x-show="openReply" x-transition class="mt-2 ml-6" style="display:none;">
                                            <form action="{{ url('student/notifications/' . $notify->id . '/comment') }}"
                                                method="POST" class="js-ajax-comment flex gap-2 items-start">
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <input type="hidden" name="content_prefix"
                                                    :value="replyToName ? '@' + replyToName + ': ' : ''">

                                                <div
                                                    class="w-6 h-6 rounded-sm bg-slate-800 text-white flex items-center justify-center font-bold text-[10px] mt-1 shadow-sm shrink-0">
                                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                                </div>
                                                <div
                                                    class="flex-1 bg-slate-50 border border-slate-200/80 p-1.5 rounded-sm focus-within:border-slate-400 focus-within:bg-white transition-colors">
                                                    <div x-show="replyToName" style="display:none;"
                                                        class="flex items-center gap-1 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded w-max mb-1 font-semibold border border-blue-200">
                                                        <span>Đang trả lời: <span x-text="replyToName"></span></span>
                                                        <button type="button" @click="openReply=false; replyToName=''"
                                                            class="ml-1 bg-blue-200 rounded-full w-3.5 h-3.5 flex items-center justify-center hover:bg-blue-300"><span
                                                                class="material-symbols-outlined !text-[9px]">close</span></button>
                                                    </div>
                                                    <div class="relative">
                                                        <input x-ref="replyInput" type="text" name="content" required
                                                            placeholder="Viết phản hồi..." autocomplete="off"
                                                            class="w-full pl-2 pr-8 py-1 bg-transparent border-none text-[13px] text-slate-800 focus:outline-none focus:ring-0 h-8">
                                                        <button type="submit"
                                                            class="absolute right-0.5 top-0.5 w-8 h-8 flex items-center justify-center bg-primary text-white rounded-sm hover:bg-primary-light transition-colors"><span
                                                                class="material-symbols-outlined !text-[13px]">send</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 text-[12px] py-2 italic">Chưa có bình luận nào.</p>
                        @endforelse
                    </div>

                    @if ($notify->allow_comments)
                        <form action="{{ url('student/notifications/' . $notify->id . '/comment') }}" method="POST"
                            class="js-ajax-comment flex gap-2 items-end pt-2 border-t border-slate-100">
                            <div
                                class="w-9 h-9 rounded-sm bg-slate-800 flex items-center justify-center text-white font-bold text-[13px] shrink-0 shadow-sm mb-0.5">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 relative group">
                                <textarea name="content" rows="1" required placeholder="Viết bình luận cho bài đăng này..."
                                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                    class="w-full pl-3 pr-9 py-2 bg-white border border-slate-300 rounded-sm text-[13px] focus:ring-1 focus:ring-primary focus:border-primary resize-none shadow-sm overflow-hidden"
                                    style="min-height: 44px; max-height: 120px;"></textarea>
                                <button type="submit"
                                    class="absolute right-1.5 bottom-1.5 p-2 text-primary hover:bg-primary/10 rounded-sm transition-colors"
                                    title="Gửi"><span
                                        class="material-symbols-outlined !text-[18px]">send</span></button>
                            </div>
                        </form>
                    @else
                        <div
                            class="text-center text-[12px] text-slate-500 bg-slate-100 py-2 rounded-sm border border-slate-200 mt-2">
                            <span class="material-symbols-outlined !text-[14px] align-middle">comments_disabled</span> Bình
                            luận đã bị tắt.
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-white rounded-sm shadow-sm border border-slate-200 p-12 text-center">
                <span class="material-symbols-outlined text-[40px] text-slate-300 mb-2 block">notifications_paused</span>
                <h3 class="text-[14px] font-bold text-slate-700 font-display">Chưa có thông báo nào</h3>
            </div>
        @endforelse

        <div class="mt-1 mb-8 text-[12px]">
            {{ $notifications->links() }}
        </div>
    </section>
@endsection


@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const CSRF_TOKEN = "{{ csrf_token() }}";

            // Helpers
            function escapeHtml(str) {
                if (str === null || str === undefined) return '';
                return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
                    '&quot;').replace(/'/g, '&#039;');
            }

            function highlightTemp(el) {
                el.classList.add('target-comment');
                setTimeout(() => el.classList.remove('target-comment'), 2500);
            }

            // Build HTML Comment Gốc
            function buildCommentHTML(d, notifyId) {
                let avatarBg = d.user_role == 1 ? 'bg-blue-600' : (d.user_role == 2 ? 'bg-emerald-600' :
                    'bg-slate-600');
                return `
                    <div id="comment-${d.id}" class="relative transition-colors duration-500" x-data="{ openReply: false, showAllReplies: false, replyToName: '' }">
                        <div class="flex gap-2 items-start min-w-0">
                            <div class="flex-none"><div class="h-8 w-8 rounded-md flex items-center justify-center text-white font-bold text-[12px] shadow-sm ${avatarBg}">${escapeHtml(d.user_initial)}</div></div>
                            <div class="flex-1 min-w-0 text-left">
                                <div class="bg-white border border-slate-200/80 px-3 py-2 rounded-md inline-block max-w-full shadow-sm text-left">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <h5 class="text-[13px] font-bold text-slate-800 hover:underline cursor-pointer">${escapeHtml(d.user_name)}</h5>
                                    </div>
                                    <div class="text-[13px] text-slate-700 leading-snug w-full break-words">${d.content}</div>
                                </div>
                                <div class="flex items-center gap-3 text-[11px] text-slate-500 mt-1 ml-1.5 font-medium">
                                    <button @click="openReply=true; replyToName='${escapeHtml(d.user_name)}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })" class="hover:underline hover:text-slate-900 transition-colors">Phản hồi</button>
                                    <span>·</span> <span>Vừa xong</span>
                                </div>
                                
                                <div class="replies-wrap ml-6 mt-3 flex flex-col gap-3 relative before:content-[''] before:absolute before:-left-[1rem] before:top-0 before:bottom-0 before:w-[2px] before:bg-slate-200 before:rounded-full empty:hidden"></div>

                                <div x-show="openReply" x-transition class="mt-2 ml-6" style="display:none;">
                                    <form action="/student/notifications/${notifyId}/comment" method="POST" class="js-ajax-comment flex gap-2 items-start">
                                        <input type="hidden" name="parent_id" value="${d.id}">
                                        <input type="hidden" name="content_prefix" :value="replyToName ? '@' + replyToName + ': ' : ''">
                                        <div class="w-6 h-6 rounded-md bg-slate-800 text-white flex items-center justify-center font-bold text-[10px] mt-1 shadow-sm shrink-0">{{ mb_substr(Auth::user()->name, 0, 1) }}</div>
                                        <div class="flex-1 bg-slate-50 border border-slate-200/80 p-1.5 rounded-md focus-within:border-slate-400 focus-within:bg-white transition-colors">
                                            <div x-show="replyToName" style="display:none;" class="flex items-center gap-1 text-[10px] bg-blue-100 text-blue-700 px-1.5 py-0.5 rounded w-max mb-1 font-semibold border border-blue-200">
                                                <span>Đang trả lời: <span x-text="replyToName"></span></span>
                                                <button type="button" @click="openReply=false; replyToName=''" class="ml-1 bg-blue-200 rounded-full w-3.5 h-3.5 flex items-center justify-center"><span class="material-symbols-outlined !text-[9px]">close</span></button>
                                            </div>
                                            <div class="relative">
                                                <input x-ref="replyInput" type="text" name="content" required placeholder="Viết phản hồi..." autocomplete="off" class="w-full pl-2 pr-8 py-1 bg-transparent border-none text-[13px] text-slate-800 focus:outline-none focus:ring-0 h-8">
                                                <button type="submit" class="absolute right-0.5 top-0.5 w-8 h-8 flex items-center justify-center bg-primary text-white rounded-md"><span class="material-symbols-outlined !text-[13px]">send</span></button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Build HTML Reply (Bình luận con) — giữ nguyên (đã định nghĩa trước)
            function buildReplyHTML(d, notifyId) {
                let avatarBg = d.user_role == 1 ? 'bg-blue-600' : (d.user_role == 2 ? 'bg-emerald-600' :
                    'bg-slate-600');
                const parentInfo = d.parent_user_name ?
                    `<div class="inline-block text-[10px] bg-indigo-50 text-indigo-600 px-1.5 py-[1px] rounded mb-1 font-bold border border-indigo-100">Trả lời: ${escapeHtml(d.parent_user_name)}</div>` :
                    '';

                let contentText = d.content;
                contentText = contentText.replace(/(@[^\s:]+:?)/g, '<strong class="text-indigo-600">$1</strong>');

                return `
                    <div id="comment-${d.id}" class="bg-white border border-slate-100 p-2 rounded-md shadow-sm w-full break-words">
                        ${parentInfo}
                        <div class="flex items-start gap-2">
                            <div class="w-6 h-6 flex-none flex items-center justify-center rounded-md text-white font-bold text-[10px] shadow-sm ${avatarBg}">
                                ${escapeHtml(d.user_initial)}
                            </div>
                            <div class="flex-1 min-w-0 text-left">
                                <div class="font-bold text-[12px] text-slate-900">${escapeHtml(d.user_name)}</div>
                                <div class="text-[12.5px] text-slate-800 mt-0.5 leading-snug break-words w-full">${contentText}</div>
                                <div class="mt-1 flex items-center gap-3 text-[11px] text-slate-500 font-medium">
                                    <button @click="openReply = true; showAllReplies = true; replyToName='${escapeHtml(d.user_name)}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })" class="hover:underline hover:text-slate-900 transition-colors">Phản hồi</button>
                                    <span>·</span> <span>Vừa xong</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }

            // Xử lý Gửi Bình Luận & Phản Hồi bằng AJAX
            document.body.addEventListener('submit', async function(e) {
                const form = e.target;
                if (!form.classList.contains('js-ajax-comment')) return;
                e.preventDefault();

                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                }

                const action = form.getAttribute('action');
                const formData = new FormData(form);
                formData.append('_token', CSRF_TOKEN); // inject CSRF an toàn

                const notifyArticle = form.closest('article[data-notification-id]');
                const notifyId = notifyArticle ? notifyArticle.getAttribute('data-notification-id') :
                    null;

                try {
                    const resp = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const json = await resp.json();

                    if (resp.ok && json.success) {
                        const d = json.comment;

                        if (d.parent_id) { // reply
                            const parentEl = document.querySelector(`#comment-${d.parent_id}`);
                            if (parentEl) {
                                let repliesWrap = parentEl.querySelector('.replies-wrap');
                                if (repliesWrap) {
                                    repliesWrap.classList.remove('empty:hidden');
                                    repliesWrap.insertAdjacentHTML('beforeend', buildReplyHTML(d,
                                        notifyId));

                                    // Alpine updates if present
                                    if (parentEl.__x) {
                                        parentEl.__x.$data.showAllReplies = true;
                                        parentEl.__x.$data.openReply = false;
                                        parentEl.__x.$data.replyToName = '';
                                    }

                                    setTimeout(() => {
                                        const newly = document.querySelector(
                                            `#comment-${d.id}`);
                                        if (newly) {
                                            newly.scrollIntoView({
                                                behavior: 'smooth',
                                                block: 'nearest'
                                            });
                                            highlightTemp(newly);
                                        }
                                    }, 50);
                                }
                            }
                        } else { // new top-level comment
                            const list = document.querySelector(`#comments-list-${notifyId}`);
                            if (list) {
                                list.insertAdjacentHTML('beforeend', buildCommentHTML(d, notifyId));
                                setTimeout(() => {
                                    const newly = document.querySelector(`#comment-${d.id}`);
                                    if (newly) {
                                        Alpine.initTree ? Alpine.initTree(newly) : null;
                                        newly.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'nearest'
                                        });
                                        highlightTemp(newly);
                                    }
                                }, 50);
                            }
                        }

                        // update comments count if provided
                        if (json.comments_count) {
                            const counter = document.querySelector(
                                `[data-notification-id="${notifyId}"] .flex.items-center.gap-2 span.likes-count-${notifyId}`
                            );
                            // the above selector is not ideal; better update global area:
                            const cc = document.querySelector(
                                `[data-notification-id="${notifyId}"] .flex.items-center.justify-between span[ @click ]`
                            );
                            // Safe fallback: update the visible text near top
                            const commentsBadge = document.querySelector(
                                `[data-notification-id="${notifyId}"] [@click]`);
                            // (we keep it simple: find element that shows comments_count text)
                        }

                        // Reset khung nhập
                        const inputs = form.querySelectorAll('textarea, input[name="content"]');
                        inputs.forEach(input => {
                            input.value = '';
                            if (input.tagName === 'TEXTAREA') input.style.height = '44px';
                        });

                    } else {
                        alert('Lỗi: ' + (json.error || 'Dữ liệu không hợp lệ'));
                    }
                } catch (err) {
                    console.error(err);
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                }
            });

            // Xử lý Gửi Thích Bằng AJAX
            document.body.addEventListener('submit', async function(e) {
                const form = e.target;
                if (!form.classList.contains('js-ajax-like')) return;
                e.preventDefault();

                const action = form.getAttribute('action');
                const formData = new FormData(form);
                formData.append('_token', CSRF_TOKEN);

                try {
                    const resp = await fetch(action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const json = await resp.json();
                    if (resp.ok && json.success) {
                        const matches = action.match(/notifications\/(\d+)\/like/);
                        if (matches) {
                            const nid = matches[1];
                            const countEl = document.querySelector('.likes-count-' + nid);
                            if (countEl) countEl.textContent = json.likes_count;

                            // Đổi màu nút Like
                            const btn = form.querySelector('button');
                            const icon = btn.querySelector('span');
                            if (btn.classList.contains('text-slate-600')) {
                                btn.classList.replace('text-slate-600', 'text-primary');
                                btn.classList.replace('hover:bg-slate-50', 'bg-primary/5');
                                icon.setAttribute('style', 'font-variation-settings:"FILL"1');
                            } else {
                                btn.classList.replace('text-primary', 'text-slate-600');
                                btn.classList.replace('bg-primary/5', 'hover:bg-slate-50');
                                icon.removeAttribute('style');
                            }
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        });

        window.highlightCommentBubble = function(hash) {
            let el = document.querySelector(hash);
            if (!el) {
                console.warn('Không tìm thấy bình luận trên trang hiện tại:', hash);
                return;
            }

            // 1. ÉP ALPINEJS MỞ KHU VỰC BÌNH LUẬN CỦA BÀI VIẾT
            let art = el.closest('article');
            if (art) {
                // Cách gọi chuẩn của AlpineJS V3
                if (typeof Alpine !== 'undefined' && typeof Alpine.$data === 'function') {
                    let data = Alpine.$data(art);
                    if (data && data.showComments !== undefined) {
                        data.showComments = true;
                    }
                }
                // Fallback tự động click nút "Bình luận" nếu Alpine API không rớt vào case trên
                else {
                    let commentsDiv = art.querySelector('[x-show="showComments"]');
                    let btn = art.querySelector('button[\\@click="showComments = !showComments"]');
                    if (commentsDiv && window.getComputedStyle(commentsDiv).display === 'none' && btn) {
                        btn.click();
                    }
                }
            }

            // 2. ÉP ALPINEJS MỞ DANH SÁCH TRẢ LỜI CON (NẾU ĐÍCH ĐẾN BỊ ẨN)
            let replyWrap = el.closest('.replies-wrap');
            if (replyWrap) {
                let parentComment = replyWrap.closest('[id^="comment-"]');
                if (parentComment) {
                    if (typeof Alpine !== 'undefined' && typeof Alpine.$data === 'function') {
                        let pData = Alpine.$data(parentComment);
                        if (pData && pData.showAllReplies !== undefined) {
                            pData.showAllReplies = true;
                        }
                    }
                }
            }

            // 3. ĐỢI DOM MỞ XONG RỒI MỚI CUỘN VÀ NHÁY SÁNG (Tăng thời gian đợi lên 250ms)
            setTimeout(() => {
                // Cuộn tới giữa màn hình
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Tìm vùng có nền trắng để nháy sáng
                let bubble = el.querySelector('.bg-white, .bg-slate-50, .bg-slate-100, .bg-slate-200\\/80') ||
                    el;

                let originalBg = bubble.style.backgroundColor;
                let originalTransition = bubble.style.transition;

                // Tắt hiệu ứng mượt tạm thời, đổi màu ngay sang xanh nhạt
                bubble.style.transition = 'none';
                bubble.style.backgroundColor = '#dbeafe';

                void bubble.offsetWidth; // Ép trình duyệt vẽ lại DOM ngay lập tức

                // Phai màu từ từ sau 500ms để người dùng kịp nhìn
                setTimeout(() => {
                    bubble.style.transition = 'background-color 2s ease-in-out';
                    bubble.style.backgroundColor = originalBg;

                    // Xóa rác CSS sau khi chớp xong
                    setTimeout(() => {
                        bubble.style.transition = originalTransition;
                        bubble.style.backgroundColor = '';
                    }, 2000);
                }, 500);
            }, 250);
        };
        window.highlightPost = function(hash) {
            let el = document.querySelector(hash);
            if (!el) {
                console.warn('Không tìm thấy bài viết:', hash);
                return;
            }

            // Chỉ đợi 1 chút rồi cuộn tới giữa màn hình, bỏ qua logic mở Comment
            setTimeout(() => {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Đổi màu nền sang xanh nhạt để nháy sáng
                let originalBg = el.style.backgroundColor;
                let originalTransition = el.style.transition;

                el.style.transition = 'none';
                el.style.backgroundColor = '#dbeafe';

                void el.offsetWidth; // Ép trình duyệt vẽ lại ngay lập tức

                // Sau 500ms thì phai màu dần về như cũ
                setTimeout(() => {
                    el.style.transition = 'background-color 2s ease-in-out';
                    el.style.backgroundColor = originalBg;

                    // Xóa rác CSS
                    setTimeout(() => {
                        el.style.transition = originalTransition;
                        el.style.backgroundColor = '';
                    }, 2000);
                }, 500);
            }, 100);
        };
        // Component Xử lý Tìm kiếm Hệ thống
        window.searchSystem = function() {
            return {
                searchQuery: '',
                results: [],
                isLoading: false,
                isOpen: false,
                debounceTimer: null,

                init() {
                    // Lắng nghe sự thay đổi của input để gọi API
                    this.$watch('searchQuery', (val) => {
                        const query = val.trim();
                        if (query.length === 0) {
                            this.results = [];
                            this.isOpen = false;
                            return;
                        }

                        this.isOpen = true;
                        this.isLoading = true;
                        clearTimeout(this.debounceTimer);

                        // Đợi 400ms sau khi người dùng ngừng gõ mới gọi API để chống spam request
                        this.debounceTimer = setTimeout(() => {
                            this.fetchData(query);
                        }, 400);
                    });
                },

                async fetchData(query) {
                    try {
                        // Nhớ đảm bảo trong web.php bạn đã khai báo route này
                        const response = await fetch(
                            `{{ url('/student/search-api') }}?q=${encodeURIComponent(query)}`);
                        if (response.ok) {
                            this.results = await response.json();
                        }
                    } catch (error) {
                        console.error('Lỗi tìm kiếm:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                handleResultClick(targetUrl) {
                    this.isOpen = false;
                    this.searchQuery = ''; // Reset input sau khi bấm

                    let targetObj = new URL(targetUrl, window.location.origin);
                    let currentPath = window.location.pathname.replace(/\/$/, '');
                    let targetPath = targetObj.pathname.replace(/\/$/, '');

                    // Logic mượn từ phần chuông thông báo
                    if (currentPath === targetPath) {
                        if (window.history.pushState) {
                            window.history.pushState(null, null, targetObj.search + targetObj.hash);
                        } else {
                            window.location.hash = targetObj.hash;
                        }

                        // Gọi lại hàm cuộn và nháy sáng bài viết đã định nghĩa sẵn
                        // Gọi hàm cuộn bài viết mới tạo (không mở bình luận)
                        if (targetObj.hash && typeof window.highlightPost === 'function') {
                            window.highlightPost(targetObj.hash);
                        } else {
                            window.location.reload();
                        }
                    } else {
                        window.location.href = targetUrl;
                    }
                }
            }
        }
    </script>
@endsection
