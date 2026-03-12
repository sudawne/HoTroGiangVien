@extends('layouts.student')
@section('title', 'Bảng tin Sinh viên')

@section('styles')
    <style>
        .target-comment {
            animation: highlightComment 2.5s ease-out forwards;
            border-radius: 0.125rem;
        }

        @keyframes highlightComment {
            0% {
                background-color: #dbeafe;
            }

            100% {
                background-color: transparent;
            }
        }

        .post-card {
            border-radius: 0.25rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .post-card .post-header h3,
        .post-card .post-header p {
            line-height: 1.05;
        }

        /* Allow notification content to expand horizontally and wrap nicely */
        .post-card .prose {
            word-wrap: break-word;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
            max-width: 100%;
        }

        /* If the content contains very long inline elements (tables, code), allow horizontal scroll */
        .post-card .prose-container {
            overflow-x: auto;
        }

        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 2px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background-color: #94a3b8;
        }

        .hide-scroll::-webkit-scrollbar {
            display: none;
        }

        .hide-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>
@endsection

@section('content')
    @php
        $student = Auth::user()->student;
        $class = $student ? $student->class : null;
        $currentFilter = request()->get('filter', 'all');
        $currentTimeFilter = request()->get('time', 'all');
        $currentSearch = request()->get('search', '');
    @endphp

    <!-- Tăng chiều ngang: max-w từ 700 -> 1000 -->
    <section class="w-full max-w-[1000px] mx-auto pt-6 pb-12 px-4 sm:px-6 flex flex-col gap-6">

        {{-- HEADER BẢNG TIN & BỘ LỌC --}}
        <div
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 rounded-sm border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3 shrink-0">
                <div class="p-2 bg-primary/10 text-primary rounded-sm">
                    <span class="material-symbols-outlined !text-[28px]">feed</span>
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-slate-800 tracking-tight leading-none mb-1">Bảng tin</h1>
                    <p class="text-xs font-medium text-slate-500">Cập nhật thông báo mới nhất</p>
                </div>
            </div>

            <div class="w-full sm:w-auto">
                <form method="GET" action="{{ url('/student') }}" class="flex items-center gap-2 w-full" id="filterFormTop">
                    @if ($currentSearch)
                        <input type="hidden" name="search" value="{{ $currentSearch }}">
                    @endif

                    {{-- Dropdown Thời gian --}}
                    <div
                        class="relative flex-1 sm:w-36 bg-slate-50 rounded-sm border border-slate-200 hover:border-primary/50 transition-colors">
                        <select name="time" onchange="document.getElementById('filterFormTop').submit()"
                            class="w-full text-[13px] font-bold border-none focus:ring-0 py-2 pl-3 pr-8 text-slate-700 bg-transparent cursor-pointer outline-none appearance-none">
                            <option value="all" {{ $currentTimeFilter == 'all' ? 'selected' : '' }}>Mọi lúc</option>
                            <option value="today" {{ $currentTimeFilter == 'today' ? 'selected' : '' }}>Hôm nay</option>
                            <option value="week" {{ $currentTimeFilter == 'week' ? 'selected' : '' }}>Tuần này</option>
                            <option value="month" {{ $currentTimeFilter == 'month' ? 'selected' : '' }}>Tháng này</option>
                        </select>
                        <span
                            class="material-symbols-outlined !text-[18px] absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">expand_more</span>
                    </div>

                    {{-- Nút Lọc Loại Bảng Tin --}}
                    <div class="flex gap-1 bg-slate-50 p-1 rounded-sm border border-slate-200">
                        <button type="submit" name="filter" value="all" title="Tất cả"
                            class="w-8 h-8 flex items-center justify-center rounded-sm transition-all {{ $currentFilter == 'all' ? 'bg-white text-primary shadow-sm border border-slate-200/60' : 'bg-transparent text-slate-400 hover:text-slate-600 hover:bg-slate-100' }}">
                            <span class="material-symbols-outlined !text-[18px]">public</span>
                        </button>
                        <button type="submit" name="filter" value="urgent" title="Khẩn cấp"
                            class="w-8 h-8 flex items-center justify-center rounded-sm transition-all {{ $currentFilter == 'urgent' ? 'bg-red-50 text-red-600 shadow-sm border border-red-100' : 'bg-transparent text-slate-400 hover:text-red-500 hover:bg-red-50' }}">
                            <span class="material-symbols-outlined !text-[18px]">error</span>
                        </button>
                        <button type="submit" name="filter" value="warning" title="Chú ý"
                            class="w-8 h-8 flex items-center justify-center rounded-sm transition-all {{ $currentFilter == 'warning' ? 'bg-orange-50 text-orange-600 shadow-sm border border-orange-100' : 'bg-transparent text-slate-400 hover:text-orange-500 hover:bg-orange-50' }}">
                            <span class="material-symbols-outlined !text-[18px]">warning</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- KẾT QUẢ TÌM KIẾM --}}
        @if ($currentSearch)
            <div
                class="bg-indigo-50 border border-indigo-200 text-indigo-800 px-5 py-4 rounded-sm flex items-center justify-between text-[14px] font-medium shadow-sm">
                <span class="flex items-center gap-2.5 min-w-0">
                    <span class="material-symbols-outlined !text-[22px] shrink-0">search</span>
                    <span class="truncate">Kết quả tìm kiếm cho: <strong
                            class="text-indigo-900 text-[15px]">"{{ $currentSearch }}"</strong></span>
                </span>
                <a href="{{ url('/student?filter=' . $currentFilter . '&time=' . $currentTimeFilter) }}"
                    class="text-indigo-500 hover:text-red-500 hover:bg-red-100 transition-colors p-1.5 rounded-full shrink-0"
                    title="Hủy tìm kiếm">
                    <span class="material-symbols-outlined !text-[18px] block">close</span>
                </a>
            </div>
        @endif

        {{-- DANH SÁCH BÀI VIẾT --}}
        @forelse($notifications as $notify)
            <article id="notification-{{ $notify->id }}" data-notification-id="{{ $notify->id }}"
                x-data="{ showComments: false }" class="post-card bg-white overflow-hidden relative transition-colors duration-500">
                <div class="p-5 sm:p-6">

                    {{-- Header bài viết --}}
                    <div class="flex justify-between items-start mb-4 post-header">
                        <div class="flex items-center gap-3.5">
                            <div
                                class="h-10 w-10 sm:h-12 sm:w-12 rounded-full text-white flex items-center justify-center font-bold text-[14px] sm:text-[16px] shadow-sm {{ ($notify->sender->role_id ?? 0) == 1 ? 'bg-blue-600' : 'bg-emerald-600' }}">
                                {{ mb_substr($notify->sender->name ?? 'A', 0, 1) }}
                            </div>
                            <div>
                                <h3
                                    class="font-bold text-[14px] sm:text-[15px] text-slate-900 hover:underline cursor-pointer leading-tight">
                                    {{ $notify->sender->name ?? 'Hệ thống' }}
                                </h3>
                                <p class="text-[12px] sm:text-[12.5px] text-slate-500 mt-1 flex items-center gap-1.5">
                                    {{ $notify->created_at->diffForHumans() }} <span>•</span>
                                    @if ($notify->target_audience == 'all')
                                        <span
                                            class="flex items-center gap-0.5 text-blue-600 font-semibold bg-blue-50 px-1.5 py-0.5 rounded-sm">
                                            <span class="material-symbols-outlined !text-[12px]">public</span> Toàn trường
                                        </span>
                                    @else
                                        <span
                                            class="flex items-center gap-0.5 text-emerald-600 font-semibold bg-emerald-50 px-1.5 py-0.5 rounded-sm">
                                            <span class="material-symbols-outlined !text-[12px]">group</span> Lớp
                                            {{ $class->code ?? '' }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        @if ($notify->type == 'urgent')
                            <span
                                class="bg-red-50 text-red-700 border border-red-100 text-[10px] sm:text-[11px] font-bold px-2 py-1 rounded-sm uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined !text-[14px]">error</span> <span
                                    class="hidden sm:inline">Khẩn cấp</span>
                            </span>
                        @elseif($notify->type == 'warning')
                            <span
                                class="bg-orange-50 text-orange-700 border border-orange-100 text-[10px] sm:text-[11px] font-bold px-2 py-1 rounded-sm uppercase flex items-center gap-1">
                                <span class="material-symbols-outlined !text-[14px]">warning</span> <span
                                    class="hidden sm:inline">Chú ý</span>
                            </span>
                        @endif
                    </div>

                    {{-- Nội dung bài viết --}}
                    <div class="mb-4 mt-2">
                        <h4 class="font-bold text-[15px] sm:text-[16px] mb-2 text-slate-900 leading-snug">
                            {{ $notify->title }}
                        </h4>
                        <div class="prose-slate max-w-none text-[14px] sm:text-[14.5px] text-slate-800 leading-relaxed prose-a:text-blue-600 hover:prose-a:underline prose-container prose-p:first-of-type:mt-0 prose-p:last-of-type:mb-0 prose-p:my-1.5 prose-ul:my-1"
                            style="word-wrap: break-word;">
                            {!! $notify->message !!}
                        </div>
                    </div>

                    {{-- Đính kèm --}}
                    @if ($notify->attachment_url)
                        <a href="{{ asset('storage/' . $notify->attachment_url) }}"
                            download="{{ $notify->attachment_name }}"
                            class="bg-slate-50 rounded-sm p-3 mb-4 flex items-center gap-3 border border-slate-200 cursor-pointer hover:bg-slate-100 hover:border-slate-300 transition-colors group">
                            <div
                                class="bg-white border border-slate-200 text-primary p-2 rounded-sm shadow-sm shrink-0 group-hover:scale-105 transition-transform">
                                <span class="material-symbols-outlined !text-[20px]">description</span>
                            </div>
                            <div class="flex-1 overflow-hidden">
                                <p class="text-[13px] font-bold text-slate-800 truncate">{{ $notify->attachment_name }}</p>
                                <p class="text-[11px] text-slate-500 mt-0.5">Nhấn để tải xuống</p>
                            </div>
                            <div class="text-slate-400 group-hover:text-primary p-1">
                                <span class="material-symbols-outlined !text-[18px]">download</span>
                            </div>
                        </a>
                    @endif

                    {{-- Thống kê Like/Comment --}}
                    <div
                        class="flex items-center justify-between text-[12.5px] font-medium text-slate-500 pb-3 border-b border-slate-100">
                        <span class="flex items-center gap-1.5">
                            <span
                                class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-blue-50 text-blue-600 ring-2 ring-white">
                                <span class="material-symbols-outlined !text-[11px]"
                                    style="font-variation-settings:'FILL' 1">thumb_up</span>
                            </span>
                            <span
                                class="likes-count-{{ $notify->id }} text-slate-700 font-bold">{{ $notify->likes_count }}</span>
                        </span>
                        <span class="flex items-center gap-1 hover:underline cursor-pointer"
                            @click="showComments = !showComments">
                            {{ $notify->comments_count }} Bình luận
                        </span>
                    </div>

                    {{-- Nút tương tác --}}
                    <div class="flex gap-2 pt-2">
                        <form action="{{ url('student/notifications/' . $notify->id . '/like') }}" method="POST"
                            class="flex-1 js-ajax-like">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center justify-center gap-2 py-2 text-[13.5px] font-bold rounded-sm transition-colors {{ $notify->isLikedBy(Auth::id()) ? 'text-blue-600 bg-blue-50/50' : 'text-slate-600 hover:bg-slate-50' }}">
                                <span class="material-symbols-outlined !text-[18px] transition-transform active:scale-125"
                                    {{ $notify->isLikedBy(Auth::id()) ? 'style=font-variation-settings:\"FILL\"1' : '' }}>thumb_up</span>
                                Thích
                            </button>
                        </form>
                        <button @click="showComments = !showComments"
                            class="flex-1 flex items-center justify-center gap-2 py-2 text-[13.5px] font-bold text-slate-600 hover:bg-slate-50 rounded-sm transition-colors">
                            <span class="material-symbols-outlined !text-[18px]">chat_bubble_outline</span> Bình luận
                        </button>
                    </div>
                </div>

                {{-- VÙNG BÌNH LUẬN --}}
                <div x-show="showComments" x-transition class="border-t border-slate-100 bg-slate-50/50 p-4 sm:p-5"
                    x-cloak>
                    @if ($notify->allow_comments)
                        <form action="{{ url('student/notifications/' . $notify->id . '/comment') }}" method="POST"
                            class="js-ajax-comment flex gap-3 items-start mb-6">
                            @csrf
                            <div
                                class="w-9 h-9 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-[13px] shrink-0 shadow-sm mt-0.5">
                                {{ mb_substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <div
                                class="flex-1 relative group bg-white border border-slate-300 rounded-sm focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20 shadow-sm transition-all overflow-hidden p-1 min-h-[42px]">
                                <textarea name="content" rows="1" required placeholder="Viết bình luận của bạn..."
                                    oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                    class="w-full pl-2 pr-10 py-1.5 bg-transparent border-none text-[13.5px] focus:ring-0 resize-none custom-scrollbar"
                                    style="min-height:30px; max-height:120px;"></textarea>
                                <button type="submit"
                                    class="absolute right-1 bottom-1 p-1 text-primary hover:bg-primary/10 rounded-sm transition-colors"
                                    title="Gửi">
                                    <span class="material-symbols-outlined !text-[18px]">send</span>
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="space-y-4 max-h-[400px] overflow-y-auto custom-scrollbar px-1 pr-2"
                        id="comments-list-{{ $notify->id }}">
                        @forelse($notify->comments as $comment)
                            @php $replyCount = isset($comment->replies) ? $comment->replies->count() : 0; @endphp
                            <div id="comment-{{ $comment->id }}" class="relative transition-colors duration-500"
                                x-data="{ openReply: false, showAllReplies: false, replyToName: '' }">
                                <div class="flex gap-2.5 items-start">
                                    <div class="relative flex flex-col items-center">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-[11px] shrink-0 shadow-sm z-10 {{ $comment->user->role_id == 3 ? 'bg-slate-600' : ($comment->user->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600') }}">
                                            {{ mb_substr($comment->user->name, 0, 1) }}
                                        </div>
                                        @if ($replyCount > 0)
                                            <div class="absolute top-8 bottom-[-15px] w-[2px] bg-slate-200 z-0"></div>
                                        @endif
                                    </div>

                                    <div class="flex-1 min-w-0 text-left">
                                        <div
                                            class="bg-white border border-slate-200 px-3 py-2 rounded-sm rounded-tl-none inline-block max-w-full shadow-sm comment-box transition-colors duration-1000">
                                            <div class="flex items-center gap-1 mb-0.5">
                                                <h5 class="text-[13px] font-bold text-slate-800">
                                                    {{ $comment->user->name }}</h5>
                                                @if ($comment->user->role_id == 1)
                                                    <span class="material-symbols-outlined text-blue-500 !text-[13px]"
                                                        title="Admin">verified</span>
                                                @elseif($comment->user->role_id == 2)
                                                    <span class="material-symbols-outlined text-emerald-500 !text-[13px]"
                                                        title="Giảng viên/Cố vấn">school</span>
                                                @endif
                                            </div>
                                            <div class="text-[13.5px] text-slate-800 leading-snug break-words">
                                                {!! nl2br(e(trim($comment->content))) !!}
                                            </div>
                                        </div>

                                        <div
                                            class="flex items-center gap-3 text-[11px] text-slate-500 mt-1 ml-1 font-bold">
                                            <button class="hover:text-slate-800 transition-colors">Thích</button>
                                            @if ($notify->allow_comments)
                                                <button
                                                    @click="openReply = true; replyToName='{{ addslashes($comment->user->name) }}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })"
                                                    class="hover:text-slate-800 transition-colors">Phản hồi</button>
                                            @endif
                                            <span
                                                class="font-medium text-[10px]">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        @if ($replyCount > 0)
                                            <div class="replies-wrap ml-2 mt-2.5 flex flex-col gap-3 relative z-10">
                                                @if ($replyCount > 1)
                                                    <button @click="showAllReplies = !showAllReplies"
                                                        class="inline-flex items-center gap-1 text-slate-500 hover:text-slate-800 font-bold text-[11.5px] py-0.5 bg-transparent w-max">
                                                        <span class="material-symbols-outlined !text-[14px]"
                                                            x-text="showAllReplies ? 'subdirectory_arrow_left' : 'subdirectory_arrow_right'"></span>
                                                        <span
                                                            x-text="showAllReplies ? 'Ẩn bớt' : 'Xem thêm {{ $replyCount - 1 }} phản hồi'"></span>
                                                    </button>
                                                @endif

                                                @foreach ($comment->replies as $index => $reply)
                                                    <div id="reply-{{ $reply->id }}"
                                                        class="flex items-start gap-2 relative transition-all duration-500"
                                                        @if ($replyCount > 1 && $index < $replyCount - 1) x-show="showAllReplies" x-transition @endif>
                                                        <div
                                                            class="absolute left-[-20px] top-[-8px] w-4 h-5 border-b-2 border-l-2 border-slate-200 rounded-bl-sm z-0">
                                                        </div>
                                                        <div
                                                            class="w-6 h-6 flex-none flex items-center justify-center rounded-full text-white font-bold text-[9px] shadow-sm relative z-10 {{ $reply->user->role_id == 3 ? 'bg-slate-600' : ($reply->user->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600') }}">
                                                            {{ mb_substr($reply->user->name, 0, 1) }}
                                                        </div>
                                                        <div class="flex-1 min-w-0">
                                                            <div
                                                                class="bg-white border border-slate-200 px-3 py-1.5 rounded-sm rounded-tl-none shadow-sm comment-box transition-colors duration-1000 inline-block max-w-full">
                                                                <h5 class="text-[12px] font-bold text-slate-800 mb-0.5">
                                                                    {{ $reply->user->name }}</h5>
                                                                <div
                                                                    class="text-[13px] text-slate-800 leading-snug break-words inline">
                                                                    {!! preg_replace(
                                                                        '/(@[^\s:]+:?)/',
                                                                        '<strong class="text-blue-600 font-semibold">$1</strong>',
                                                                        nl2br(e(trim($reply->content))),
                                                                    ) !!}
                                                                </div>
                                                            </div>
                                                            <div class="text-[9px] text-slate-400 mt-0.5 ml-1 font-medium">
                                                                {{ $reply->created_at->diffForHumans() }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div x-show="openReply" x-transition class="mt-2.5 ml-2 relative z-10"
                                            style="display:none;">
                                            <div
                                                class="absolute left-[-20px] top-[-5px] w-4 h-6 border-b-2 border-l-2 border-slate-200 rounded-bl-sm z-0">
                                            </div>
                                            <form action="{{ url('student/notifications/' . $notify->id . '/comment') }}"
                                                method="POST"
                                                class="js-ajax-comment flex gap-2 items-start relative z-10">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <input type="hidden" name="content_prefix"
                                                    :value="replyToName ? '@' + replyToName + ' - ' : ''">

                                                <div
                                                    class="w-6 h-6 flex-none rounded-full bg-slate-800 text-white flex items-center justify-center font-bold text-[9px] mt-0.5 shadow-sm">
                                                    {{ mb_substr(Auth::user()->name, 0, 1) }}
                                                </div>
                                                <div
                                                    class="flex-1 bg-white border border-slate-300 p-0.5 rounded-sm focus-within:border-primary focus-within:ring-1 focus-within:ring-primary/20 transition-all shadow-sm">
                                                    <div x-show="replyToName" style="display:none;"
                                                        class="flex items-center gap-1 text-[10px] bg-blue-50 text-blue-600 px-2 py-0.5 rounded-sm w-max ml-1 mt-1 font-semibold">
                                                        <span>Trả lời: <span x-text="replyToName"></span></span>
                                                        <button type="button" @click="openReply=false; replyToName=''"
                                                            class="ml-0.5 hover:text-red-500 flex items-center"><span
                                                                class="material-symbols-outlined !text-[12px]">cancel</span></button>
                                                    </div>
                                                    <div class="relative flex items-center">
                                                        <input x-ref="replyInput" type="text" name="content" required
                                                            placeholder="Viết phản hồi..." autocomplete="off"
                                                            class="w-full pl-2 pr-8 py-1 bg-transparent border-none text-[12.5px] text-slate-800 focus:outline-none focus:ring-0">
                                                        <button type="submit"
                                                            class="absolute right-0.5 w-6 h-6 flex items-center justify-center text-primary hover:bg-slate-100 rounded-sm transition-colors"><span
                                                                class="material-symbols-outlined !text-[14px]">send</span></button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 text-[12.5px] py-4">Chưa có bình luận nào. Hãy là
                                người đầu tiên!</p>
                        @endforelse
                    </div>

                    @if (!$notify->allow_comments)
                        <div
                            class="text-center text-[12.5px] text-slate-500 bg-slate-100 py-2.5 rounded-sm border border-slate-200 mt-4 font-medium flex items-center justify-center gap-1.5">
                            <span class="material-symbols-outlined !text-[16px]">comments_disabled</span> Tính năng bình
                            luận đã tắt.
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-white rounded-sm shadow-sm border border-slate-200 p-12 text-center mt-2">
                <span class="material-symbols-outlined text-[40px] text-slate-300 mb-3 block">
                    {{ $currentSearch ? 'search_off' : 'notifications_paused' }}
                </span>
                <h3 class="text-[15px] font-bold text-slate-700 font-display">
                    {{ $currentSearch ? 'Không tìm thấy thông báo phù hợp' : 'Chưa có thông báo nào trên hệ thống' }}
                </h3>
            </div>
        @endforelse

        <div class="mt-2 mb-8 text-[13px]">
            {{ $notifications->appends(request()->query())->links() }}
        </div>

    </section>
    <div x-data="appointmentModal()" @open-appointment.window="openModal($event.detail)" x-show="isOpen"
        style="display: none;" class="fixed inset-0 z-[110] overflow-y-auto font-sans">

        {{-- Màn mờ --}}
        <div x-show="isOpen" x-transition.opacity
            class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="isOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200">

                <div class="bg-blue-600 px-5 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <span class="material-symbols-outlined">edit_calendar</span>
                        Đặt lịch hẹn Cố vấn
                    </h3>
                    <button @click="isOpen = false" class="text-blue-100 hover:text-white transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Ngày hẹn <span
                                    class="text-red-500">*</span></label>
                            <input type="date" x-model="form.date" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1">Thời gian <span
                                    class="text-red-500">*</span></label>
                            <input type="time" x-model="form.time" required
                                class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Vấn đề cần tư vấn <span
                                class="text-red-500">*</span></label>
                        <select x-model="form.topic" required
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm">
                            <option value="Học vụ & Đăng ký tín chỉ">Học vụ & Đăng ký tín chỉ</option>
                            <option value="Điểm rèn luyện & Ngoại khóa">Điểm rèn luyện & Ngoại khóa</option>
                            <option value="Khó khăn cá nhân / Tâm lý">Khó khăn cá nhân / Tâm lý</option>
                            <option value="Hướng nghiệp & Thực tập">Hướng nghiệp & Thực tập</option>
                            <option value="Khác">Khác (Ghi rõ ở chú thích)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">Chú thích thêm</label>
                        <textarea x-model="form.note" rows="3" placeholder="Ví dụ: Em muốn hỏi về việc hủy học phần..."
                            class="w-full rounded-lg border-slate-300 text-sm focus:border-blue-500 focus:ring-blue-500 shadow-sm resize-none"></textarea>
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-slate-100">
                        <button type="button" @click="isOpen = false"
                            class="px-4 py-2 text-sm font-medium text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                            Hủy bỏ
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm flex items-center gap-1.5 transition-colors">
                            <span class="material-symbols-outlined !text-[18px]">send</span> Tạo tin nhắn
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // SCRIPT CỦA MODAL ĐẶT LỊCH HẸN
        document.addEventListener('alpine:init', () => {
            Alpine.data('appointmentModal', () => ({
                isOpen: false,
                advisorId: null,
                advisorName: '',
                form: {
                    date: '',
                    time: '',
                    topic: 'Học vụ & Đăng ký tín chỉ',
                    note: ''
                },

                openModal(data) {
                    this.advisorId = data.id;
                    this.advisorName = data.name;
                    // Reset form: Set mặc định ngày mai
                    let tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    this.form.date = tomorrow.toISOString().split('T')[0];
                    this.form.time = '09:00';
                    this.form.note = '';
                    this.isOpen = true;
                },

                submitForm() {
                    // Đổi format ngày (YYYY-MM-DD -> DD/MM/YYYY)
                    const dateParts = this.form.date.split('-');
                    const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;

                    // Tạo đoạn văn bản tin nhắn
                    let message = `Dạ em chào Thầy/Cô ${this.advisorName},\n`;
                    message += `Em muốn xin phép đặt lịch hẹn để nhờ Thầy/Cô tư vấn ạ.\n`;
                    message += `📍 Thời gian: ${this.form.time}, ngày ${formattedDate}\n`;
                    message += `📑 Vấn đề: ${this.form.topic}\n`;
                    if (this.form.note.trim() !== '') {
                        message += `💡 Ghi chú: ${this.form.note.trim()}\n`;
                    }
                    message +=
                        `\nKhông biết Thầy/Cô có tiện vào khoảng thời gian này không ạ? Em cảm ơn Thầy/Cô!`;

                    // Bắn sự kiện sang Khung Chat
                    window.dispatchEvent(new CustomEvent('fill-chat-appointment', {
                        detail: {
                            contactId: this.advisorId,
                            message: message
                        }
                    }));

                    this.isOpen = false;
                }
            }));
        });
    </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('liveSearch', () => ({
                query: '',
                results: [],
                loading: false,
                showPopup: false,
                fetchData() {
                    let q = this.query.trim();
                    if (q.length < 2) {
                        this.results = [];
                        this.showPopup = false;
                        return;
                    }
                    this.loading = true;
                    this.showPopup = true;
                    fetch(`{{ url('/student/search-api') }}?q=${encodeURIComponent(q)}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json()).then(data => {
                            this.results = data;
                            this.loading = false;
                        }).catch(err => {
                            console.error(err);
                            this.loading = false;
                        });
                },
                goToPost(targetUrl) {
                    this.showPopup = false;
                    let targetObj = new URL(targetUrl, window.location.origin);
                    if (window.location.pathname === targetObj.pathname) {
                        if (window.history.pushState) window.history.pushState(null, null, targetObj
                            .search + targetObj.hash);
                        else window.location.hash = targetObj.hash;
                        if (targetObj.hash && typeof window.highlightCommentBubble === 'function')
                            window.highlightCommentBubble(targetObj.hash);
                        else window.location.reload();
                    } else {
                        window.location.href = targetUrl;
                    }
                }
            }));
        });

        window.highlightCommentBubble = function(hash) {
            let el = document.querySelector(hash);
            if (!el && hash.startsWith('#notification-')) {
                let id = hash.replace('#notification-', '');
                el = document.querySelector(`article[data-notification-id="${id}"]`);
            }
            if (!el) return;
            let art = el.closest('article');
            if (art) {
                if (art.__x !== undefined) art.__x.$data.showComments = true;
                else if (typeof Alpine !== 'undefined') {
                    let data = Alpine.$data(art);
                    if (data) data.showComments = true;
                }
            }
            let replyWrap = el.closest('.replies-wrap');
            if (replyWrap) {
                let parentComment = replyWrap.closest('[id^="comment-"]');
                if (parentComment) {
                    if (parentComment.__x !== undefined) parentComment.__x.$data.showAllReplies = true;
                    else if (typeof Alpine !== 'undefined') {
                        let pData = Alpine.$data(parentComment);
                        if (pData) pData.showAllReplies = true;
                    }
                }
            }
            setTimeout(() => {
                el.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                let bubble = el.tagName === 'ARTICLE' ? el : (el.classList.contains('bg-white') ? el : (el
                    .querySelector('.bg-white, .bg-slate-50') || el));
                let originalBg = bubble.style.backgroundColor;
                let originalTransition = bubble.style.transition;
                bubble.style.transition = 'none';
                bubble.style.backgroundColor = '#dbeafe';
                void bubble.offsetWidth;
                setTimeout(() => {
                    bubble.style.transition = 'background-color 2.5s ease-out';
                    bubble.style.backgroundColor = originalBg || 'transparent';
                    setTimeout(() => {
                        bubble.style.transition = originalTransition;
                        bubble.style.backgroundColor = '';
                    }, 2500);
                }, 400);
            }, 250);
        };

        document.addEventListener('DOMContentLoaded', function() {
            const CSRF_TOKEN = "{{ csrf_token() }}";
            if (window.location.hash) setTimeout(() => {
                window.highlightCommentBubble(window.location.hash);
            }, 400);
            window.addEventListener('hashchange', function() {
                if (window.location.hash) window.highlightCommentBubble(window.location.hash);
            });

            document.body.addEventListener('submit', async function(e) {
                const form = e.target;
                if (!form.classList.contains('js-ajax-comment')) return;
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.style.opacity = '0.5';
                }
                const formData = new FormData(form);
                formData.append('_token', CSRF_TOKEN);
                try {
                    const resp = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const json = await resp.json();
                    if (resp.ok && json.success) window.location.reload();
                    else alert('Lỗi: ' + (json.error || 'Dữ liệu không hợp lệ'));
                } catch (err) {
                    console.error(err);
                    window.location.reload();
                } finally {
                    if (btn) {
                        btn.disabled = false;
                        btn.style.opacity = '1';
                    }
                }
            });

            document.body.addEventListener('submit', async function(e) {
                const form = e.target;
                if (!form.classList.contains('js-ajax-like')) return;
                e.preventDefault();
                const formData = new FormData(form);
                formData.append('_token', CSRF_TOKEN);
                try {
                    const resp = await fetch(form.getAttribute('action'), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    const json = await resp.json();
                    if (resp.ok && json.success) {
                        const matches = form.getAttribute('action').match(/notifications\/(\d+)\/like/);
                        if (matches) {
                            const nid = matches[1];
                            document.querySelectorAll('.likes-count-' + nid).forEach(el => el
                                .textContent = json.likes_count);
                            const btn = form.querySelector('button');
                            const icon = btn.querySelector('span');
                            if (btn.classList.contains('text-slate-600')) {
                                btn.classList.replace('text-slate-600', 'text-blue-600');
                                btn.classList.replace('hover:bg-slate-50', 'bg-blue-50/50');
                                icon.setAttribute('style', 'font-variation-settings:"FILL"1');
                            } else {
                                btn.classList.replace('text-blue-600', 'text-slate-600');
                                btn.classList.replace('bg-blue-50/50', 'hover:bg-slate-50');
                                icon.removeAttribute('style');
                            }
                        }
                    }
                } catch (err) {
                    console.error(err);
                }
            });
        });
    </script>
@endsection
