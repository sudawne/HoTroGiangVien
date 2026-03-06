@extends('layouts.admin')
@section('title', $notification->title)

@section('styles')
    <style>
        /* Chỉ giữ lại Import Font và các Animation bắt buộc */
        @import url('https://fonts.googleapis.com/css2?family=Lexend:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap');

        .font-lexend {
            font-family: 'Lexend', sans-serif;
        }

        .font-inter {
            font-family: 'Inter', sans-serif;
        }

        @keyframes highlightComment {
            0% {
                background-color: #eff6ff;
            }

            100% {
                background-color: transparent;
            }
        }

        .target-comment {
            animation: highlightComment 2.5s ease-out forwards;
            border-radius: 0.5rem;
        }

        /* Post content fixing */
        .prose-content p {
            margin-top: 0;
            margin-bottom: 1em;
        }

        .prose-content p:last-child {
            margin-bottom: 0;
        }

        /* small helper to ensure comments area doesn't get unexpected horizontal scroll */
        .comments-wrap {
            width: 100%;
            overflow: visible;
        }
    </style>
@endsection

@section('content')
    <div class="w-full min-h-screen bg-white font-inter pb-16 m-0">
        <main class="mx-auto flex w-full max-w-none flex-col px-6 md:px-10 py-8">

            <div class="flex items-center justify-between mb-6 font-lexend">
                <a href="{{ route('admin.notifications.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-slate-800 hover:bg-slate-100 py-1.5 px-3 rounded-lg transition-colors -ml-3">
                    <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
                    Quay lại danh sách
                </a>

                @if ($notification->status == 'pending' && Auth::user()->role_id == 1)
                    <form id="approve-form-show" action="{{ route('admin.notifications.approve', $notification->id) }}"
                        method="POST" class="hidden">@csrf</form>
                    <button type="button"
                        onclick="showConfirm('Xác nhận Duyệt','Xuất bản thông báo và tự động gửi Email đến sinh viên. Hành động này không thể hoàn tác.', () => document.getElementById('approve-form-show').submit(), 'primary')"
                        class="flex items-center gap-2 rounded-md bg-primary px-4 py-2.5 text-sm font-bold text-white hover:bg-primary/90 transition-all shadow-sm">
                        <span class="material-symbols-outlined !text-[18px]">send</span>
                        <span class="hidden sm:inline">Duyệt & Xuất bản</span>
                    </button>
                @endif
            </div>

            {{-- POST --}}
            <article class="flex flex-col mb-8 pb-6 border-b border-slate-200">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="h-11 w-11 shrink-0 overflow-hidden rounded-lg bg-primary text-white flex items-center justify-center font-bold text-lg shadow-sm">
                        {{ substr($notification->sender->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex flex-col justify-center">
                        <div class="flex items-center gap-2">
                            <span
                                class="font-bold text-slate-900 text-[15px] leading-none">{{ $notification->sender->name ?? 'Hệ thống' }}</span>
                            @if (($notification->sender->role_id ?? 0) == 1)
                                <span class="material-symbols-outlined text-blue-500 !text-[16px]"
                                    title="Quản trị viên">verified</span>
                            @endif
                            <span class="text-slate-500 text-[13px]">đã đăng một thông báo</span>
                        </div>
                        <div class="flex items-center gap-2 text-[13px] text-slate-500 font-medium mt-1.5">
                            <span>{{ $notification->created_at->format('d/m/Y \l\ú\c H:i') }}</span>
                            <span>•</span>
                            <span class="flex items-center gap-1 text-slate-700 bg-slate-100 px-1.5 py-0.5 rounded">
                                <span class="material-symbols-outlined !text-[14px]">group</span>
                                {{ $notification->target_audience == 'all' ? 'Toàn Trường' : 'Lớp ' . $notification->classes->pluck('code')->implode(', ') }}
                            </span>
                            <span>•</span>
                            <span
                                class="font-semibold {{ $notification->status == 'approved' ? 'text-green-600' : ($notification->status == 'pending' ? 'text-amber-600' : 'text-slate-500') }}">
                                {{ $notification->status == 'approved' ? 'Đã xuất bản' : ($notification->status == 'pending' ? 'Đang chờ duyệt' : 'Bản nháp') }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-2 mb-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-900 mb-4 font-lexend leading-snug">
                        {{ $notification->title }}</h2>
                    <div class="prose prose-slate max-w-none text-[15px] text-slate-800 leading-relaxed prose-content"
                        style="word-wrap:break-word;">
                        {!! $notification->message !!}
                    </div>
                </div>

                @if ($notification->attachment_url)
                    <div
                        class="mt-2 mb-4 rounded-lg border border-slate-200 overflow-hidden hover:bg-slate-50 transition-colors">
                        <a href="{{ asset('storage/' . $notification->attachment_url) }}"
                            download="{{ $notification->attachment_name }}" class="flex items-center p-4 gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-500 shrink-0 border border-slate-200">
                                <span class="material-symbols-outlined !text-[24px]">description</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-800 truncate">{{ $notification->attachment_name }}
                                </p>
                                <p class="text-xs text-slate-500 mt-0.5">Tài liệu đính kèm • Click để tải xuống</p>
                            </div>
                            <div
                                class="w-8 h-8 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-600 shrink-0 transition-colors">
                                <span class="material-symbols-outlined !text-[20px]">download</span>
                            </div>
                        </a>
                    </div>
                @endif

                <div
                    class="flex items-center justify-between text-slate-500 text-[13px] font-medium mt-4 pb-3 border-b border-slate-200">
                    <div class="flex items-center gap-1.5">
                        <div
                            class="w-5 h-5 rounded-full bg-blue-500 flex items-center justify-center text-white ring-2 ring-white">
                            <span class="material-symbols-outlined !text-[12px]"
                                style="font-variation-settings:'FILL' 1">thumb_up</span>
                        </div>
                        <span id="likes-count">{{ $notification->likes_count }}</span>
                    </div>
                    <div>
                        <span id="comments-count"
                            class="hover:underline cursor-pointer">{{ $notification->comments_count }} bình luận</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-b border-slate-200 pb-2">
                    <form id="like-form" action="{{ route('admin.notifications.like', $notification->id) }}" method="POST"
                        class="flex-1">@csrf
                        <button id="like-button" type="submit"
                            class="w-full flex items-center justify-center gap-2 py-2 rounded-lg hover:bg-slate-100 text-[14px] font-semibold transition-colors {{ $notification->isLikedBy(Auth::id()) ? 'text-blue-600' : 'text-slate-600' }}">
                            <span id="like-icon" class="material-symbols-outlined !text-[20px]"
                                {!! $notification->isLikedBy(Auth::id()) ? 'style="font-variation-settings: \'FILL\' 1"' : '' !!}>thumb_up</span>
                            Thích
                        </button>
                    </form>

                    <button type="button" onclick="document.getElementById('main-comment-input').focus()"
                        class="flex-1 flex items-center justify-center gap-2 py-2 rounded-lg hover:bg-slate-100 text-slate-600 text-[14px] font-semibold transition-colors">
                        <span class="material-symbols-outlined !text-[20px]">chat_bubble_outline</span>
                        Bình luận
                    </button>
                </div>
            </article>

            {{-- Comments --}}
            <section class="flex flex-col comments-wrap">
                @if ($notification->allow_comments)
                    {{-- Form tạo comment gốc --}}
                    <div class="flex gap-3 mb-8 items-start">
                        <div
                            class="h-10 w-10 shrink-0 overflow-hidden rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-sm shadow-sm mt-0.5">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <form id="main-comment-form"
                                action="{{ route('admin.notifications.comment', $notification->id) }}" method="POST"
                                class="relative group bg-slate-100 rounded-lg px-3 py-2.5 border border-slate-200 focus-within:border-slate-400 focus-within:bg-white transition-all duration-200 min-h-[44px] focus-within:min-h-[85px] focus-within:pb-10">
                                @csrf
                                <textarea id="main-comment-input" name="content" required placeholder="Viết bình luận..." rows="1"
                                    class="w-full bg-transparent border-none p-0 text-[14.5px] text-slate-800 placeholder:text-slate-500 focus:outline-none focus:ring-0 resize-none overflow-hidden"
                                    oninput="this.style.height=''; this.style.height=this.scrollHeight + 'px'"></textarea>

                                <div
                                    class="absolute right-2 bottom-2 opacity-0 invisible group-focus-within:opacity-100 group-focus-within:visible transition-all duration-200">
                                    <button type="submit"
                                        class="px-3 py-1.5 bg-violet-700 text-white text-xs font-bold rounded-lg hover:bg-violet-800 transition-colors shadow-sm flex items-center gap-1">
                                        Gửi <span class="material-symbols-outlined !text-[14px]">send</span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Danh sách comment --}}
                    <div id="comments-list" class="flex flex-col gap-6">
                        @forelse($notification->comments as $comment)
                            @php $replyCount = isset($comment->replies) ? $comment->replies->count() : 0; @endphp

                            <div id="comment-{{ $comment->id }}" class="relative p-2 -mx-2 transition-colors duration-500"
                                x-data="{ openReply: false, showAllReplies: false, replyToName: '' }">
                                <div class="flex gap-3 items-start min-w-0">
                                    {{-- Avatar --}}
                                    <div class="flex-none">
                                        <div
                                            class="h-10 w-10 overflow-hidden rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-300">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                    </div>

                                    {{-- Body --}}
                                    <div class="flex-1 min-w-0">
                                        <div
                                            class="block bg-slate-50 border border-slate-200/80 rounded-xl py-2 px-3.5 max-w-full break-words text-left">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <span
                                                    class="font-bold text-[14px] text-slate-900">{{ $comment->user->name }}</span>
                                                @if ($comment->user->role_id == 1)
                                                    <span class="material-symbols-outlined text-blue-500 !text-[14px]"
                                                        title="Admin">verified</span>
                                                @elseif ($comment->user->role_id == 2)
                                                    <span class="material-symbols-outlined text-emerald-500 !text-[14px]"
                                                        title="Giảng viên">school</span>
                                                @endif
                                                <span
                                                    class="text-[12px] text-slate-500 ml-1">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>

                                            <div
                                                class="mt-1 text-[14.5px] text-slate-800 leading-relaxed break-words w-full">
                                                {!! nl2br(e(trim($comment->content))) !!}
                                            </div>

                                            <div class="mt-1.5 flex gap-3 font-bold text-[13px] text-slate-500">
                                                <button
                                                    @click="openReply=true; replyToName='{{ $comment->user->name }}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })"
                                                    class="hover:underline hover:text-slate-900 transition-colors">Phản
                                                    hồi</button>
                                                <span>·</span>
                                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>

                                        {{-- Replies --}}
                                        @if ($replyCount > 0)
                                            <div
                                                class="ml-10 mt-3 flex flex-col gap-3 relative before:content-[''] before:absolute before:-left-[1.35rem] before:top-0 before:bottom-0 before:w-[2px] before:bg-slate-200 before:rounded-full">
                                                @if ($replyCount > 1)
                                                    <button @click="showAllReplies = !showAllReplies"
                                                        class="inline-flex items-center gap-2 text-slate-500 hover:text-slate-800 font-bold text-[13px] py-1 transition-colors bg-white pr-2 rounded w-max">
                                                        <span class="material-symbols-outlined !text-[16px] text-slate-400"
                                                            x-text="showAllReplies ? 'subdirectory_arrow_left' : 'subdirectory_arrow_right'"></span>
                                                        <span
                                                            x-text="showAllReplies ? 'Ẩn bớt phản hồi' : 'Xem {{ $replyCount - 1 }} phản hồi trước'"></span>
                                                    </button>
                                                @endif

                                                @foreach ($comment->replies as $index => $reply)
                                                    <div id="comment-{{ $reply->id }}"
                                                        class="bg-white border border-slate-100 shadow-sm rounded-xl p-2.5 flex gap-3 items-start w-full break-words"
                                                        @if ($replyCount > 1 && $index < $replyCount - 1) x-show="showAllReplies" x-transition @endif>
                                                        <div
                                                            class="w-8 h-8 flex-none flex items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-bold text-sm border border-slate-200">
                                                            {{ substr($reply->user->name, 0, 1) }}
                                                        </div>
                                                        <div class="flex-1 min-w-0 text-left">
                                                            @if (!empty($reply->parent) && !empty($reply->parent->user))
                                                                <div
                                                                    class="inline-block text-[11.5px] bg-indigo-50 text-indigo-600 px-2 py-0.5 rounded-md mb-1 font-bold border border-indigo-100">
                                                                    Trả lời: {{ $reply->parent->user->name }}
                                                                </div>
                                                            @endif

                                                            <div class="flex flex-col gap-0.5 items-start w-full">
                                                                <span
                                                                    class="font-bold text-[14px] text-slate-900">{{ $reply->user->name }}</span>
                                                                <div
                                                                    class="text-[14.5px] text-slate-800 leading-relaxed break-words w-full">
                                                                    {!! preg_replace(
                                                                        '/(@[^\s:]+:?)/',
                                                                        '<strong class="text-indigo-600">$1</strong>',
                                                                        nl2br(e(trim($reply->content))),
                                                                    ) !!}
                                                                </div>
                                                            </div>

                                                            <div
                                                                class="mt-2 flex gap-3 font-bold text-[13px] text-slate-500">
                                                                <button
                                                                    @click="openReply = true; showAllReplies = true; replyToName='{{ $reply->user->name }}'; $nextTick(()=>{ $refs.replyInput && $refs.replyInput.focus(); })"
                                                                    class="hover:underline hover:text-slate-900 transition-colors">Phản
                                                                    hồi</button>
                                                                <span>·</span>
                                                                <span>{{ $reply->created_at->diffForHumans() }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Reply form --}}
                                        <div x-show="openReply" x-transition class="mt-3 ml-10" style="display:none;">
                                            <form action="{{ route('admin.notifications.comment', $notification->id) }}"
                                                method="POST" class="reply-form flex gap-2 items-start">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div
                                                    class="h-8 w-8 shrink-0 overflow-hidden rounded-lg bg-slate-800 text-white flex items-center justify-center font-bold text-xs mt-1">
                                                    {{ substr(Auth::user()->name, 0, 1) }}
                                                </div>

                                                <div class="flex-1">
                                                    <div
                                                        class="bg-slate-50 border border-slate-200/80 p-1.5 rounded-lg focus-within:border-slate-400 focus-within:bg-white transition-colors">
                                                        <div x-show="replyToName" style="display:none;"
                                                            class="flex items-center gap-1 text-[11px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded w-max mb-1 font-semibold border border-blue-200">
                                                            <span>Đang trả lời: <span x-text="replyToName"></span></span>
                                                            <button type="button"
                                                                @click="openReply=false; replyToName=''"
                                                                class="ml-2 bg-blue-200 rounded-full w-4 h-4 flex items-center justify-center hover:bg-blue-300">
                                                                <span
                                                                    class="material-symbols-outlined !text-[10px]">close</span>
                                                            </button>
                                                        </div>

                                                        <div class="relative">
                                                            <input type="hidden" name="content_prefix"
                                                                :value="replyToName ? '@' + replyToName + ': ' : ''">
                                                            <input x-ref="replyInput" type="text" name="content"
                                                                required placeholder="Viết phản hồi..." autocomplete="off"
                                                                class="w-full pl-2 pr-10 py-1 bg-transparent border-none text-[14px] text-slate-800 focus:outline-none focus:ring-0 h-8">
                                                            <button type="submit"
                                                                class="absolute right-1 top-1 w-7 h-7 flex items-center justify-center bg-violet-700 text-white rounded-md hover:bg-violet-800 transition-colors">
                                                                <span
                                                                    class="material-symbols-outlined !text-[14px]">send</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-10 border border-dashed border-slate-300 rounded-xl bg-slate-50">
                                <p class="text-slate-500 text-[14.5px]">Chưa có bình luận nào. Hãy là người đầu tiên tham
                                    gia thảo luận!</p>
                            </div>
                        @endforelse
                    </div>
                @else
                    <div class="text-center py-6 px-4 bg-slate-50 rounded-lg mt-4 border border-slate-200">
                        <span
                            class="material-symbols-outlined text-[28px] text-slate-400 mb-1 block">comments_disabled</span>
                        <p class="text-slate-500 font-medium text-sm">Tính năng bình luận đã bị tắt cho thông báo này.</p>
                    </div>
                @endif
            </section>
        </main>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll to hash and highlight
            if (window.location.hash) {
                let targetId = window.location.hash;
                let targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.classList.add('target-comment');
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                }
            }

            // Helper: check response is JSON
            function isJsonResponse(res) {
                const ct = res.headers.get('content-type') || '';
                return ct.indexOf('application/json') !== -1;
            }

            // AJAX Like button (graceful fallback to full POST if server doesn't return JSON)
            const likeForm = document.getElementById('like-form');
            if (likeForm) {
                likeForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const url = this.action;
                    const tokenInput = this.querySelector('input[name="_token"]');
                    const token = tokenInput ? tokenInput.value : document.querySelector(
                        'meta[name="csrf-token"]')?.content;

                    // disable button while sending
                    const btn = document.getElementById('like-button');
                    btn.disabled = true;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token || '',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin'
                        })
                        .then(res => {
                            if (!isJsonResponse(res)) {
                                // fallback: reload (server likely redirected)
                                window.location.reload();
                                return;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.success) {
                                const likesCountEl = document.getElementById('likes-count');
                                if (likesCountEl && typeof data.likes_count !== 'undefined') {
                                    likesCountEl.textContent = data.likes_count;
                                }
                                const likeBtn = document.getElementById('like-button');
                                const likeIcon = document.getElementById('like-icon');
                                if (data.liked) {
                                    likeBtn.classList.remove('text-slate-600');
                                    likeBtn.classList.add('text-blue-600');
                                    if (likeIcon) likeIcon.style.fontVariationSettings = "'FILL' 1";
                                } else {
                                    likeBtn.classList.remove('text-blue-600');
                                    likeBtn.classList.add('text-slate-600');
                                    if (likeIcon) likeIcon.style.fontVariationSettings = "''";
                                }
                            } else {
                                // if server returned success=false, fallback to reload or show message
                                if (data.message) {
                                    alert(data.message);
                                } else {
                                    window.location.reload();
                                }
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            // network or parsing error — reload to ensure consistent UI
                            window.location.reload();
                        })
                        .finally(() => {
                            btn.disabled = false;
                        });
                });
            }

            // AJAX Comment submit (applies to main comment form and reply forms)
            const commentForms = document.querySelectorAll('form[action$="/comment"]');
            commentForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // if form is the main comment form or a reply form — try AJAX
                    e.preventDefault();

                    const url = this.action;
                    const tokenInput = this.querySelector('input[name="_token"]');
                    const token = tokenInput ? tokenInput.value : document.querySelector(
                        'meta[name="csrf-token"]')?.content;
                    const submitBtn = this.querySelector('button[type="submit"]') || this
                        .querySelector('input[type="submit"]');

                    // collect form data
                    const fd = new FormData(this);

                    // disable submit
                    if (submitBtn) submitBtn.disabled = true;

                    fetch(url, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token || '',
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            },
                            credentials: 'same-origin',
                            body: fd
                        })
                        .then(res => {
                            if (!isJsonResponse(res)) {
                                // fallback to full submit (postback)
                                this.submit();
                                return;
                            }
                            return res.json();
                        })
                        .then(data => {
                            if (!data) return;
                            if (data.success) {
                                // If server returned comment payload, render it inline. Expect structure:
                                // data.comment: { id, user_name, user_initial, content (HTML), created_at, parent_id }
                                // data.comments_count: integer (optional)
                                const c = data.comment;
                                if (c && typeof c.id !== 'undefined') {
                                    // create element
                                    const wrapper = document.createElement('div');
                                    wrapper.className =
                                        'relative p-2 -mx-2 transition-colors duration-500 target-comment';
                                    wrapper.id = 'comment-' + c.id;

                                    if (!c.parent_id) {
                                        // top-level comment: insert at beginning of #comments-list
                                        wrapper.innerHTML = `
                                            <div class="flex gap-3 items-start min-w-0">
                                                <div class="flex-none">
                                                    <div class="h-10 w-10 overflow-hidden rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-sm border border-slate-300">
                                                    ${c.user_initial || ''}
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="block bg-slate-50 border border-slate-200/80 rounded-xl py-2 px-3.5 max-w-full break-words text-left">
                                                        <div class="flex items-center gap-2 flex-wrap">
                                                            <span class="font-bold text-[14px] text-slate-900">${c.user_name || ''}</span>
                                                            <span class="text-[12px] text-slate-500 ml-1">${c.created_at || ''}</span>
                                                        </div>
                                                        <div class="mt-1 text-[14.5px] text-slate-800 leading-relaxed break-words w-full">
                                                            ${c.content || ''}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`;
                                        const list = document.getElementById('comments-list');
                                        if (list) list.insertBefore(wrapper, list.firstChild);
                                    } else {
                                        // reply: try to find parent comment and append into its replies area
                                        const parentEl = document.getElementById('comment-' + c
                                            .parent_id);
                                        if (parentEl) {
                                            // find or create replies container under parent
                                            let repliesContainer = parentEl.querySelector(
                                                '.replies-container');
                                            if (!repliesContainer) {
                                                repliesContainer = document.createElement(
                                                    'div');
                                                repliesContainer.className =
                                                    'ml-10 mt-3 flex flex-col gap-3 relative before:content-[\'\'] before:absolute before:-left-[1.35rem] before:top-0 before:bottom-0 before:w-[2px] before:bg-slate-200 before:rounded-full replies-container';
                                                // insert after the main body of parent
                                                const parentBody = parentEl.querySelector(
                                                    '.flex-1');
                                                if (parentBody) parentBody.appendChild(
                                                    repliesContainer);
                                                else parentEl.appendChild(repliesContainer);
                                            }

                                            const replyHtml = document.createElement('div');
                                            replyHtml.id = 'comment-' + c.id;
                                            replyHtml.className =
                                                'bg-white border border-slate-100 shadow-sm rounded-xl p-2.5 flex gap-3 items-start w-full break-words';
                                            replyHtml.innerHTML = `
                                                <div class="w-8 h-8 flex-none flex items-center justify-center rounded-lg bg-slate-100 text-slate-700 font-bold text-sm border border-slate-200">
                                                    ${c.user_initial || ''}
                                                </div>
                                                <div class="flex-1 min-w-0 text-left">
                                                    <div class="flex flex-col gap-0.5 items-start w-full">
                                                        <span class="font-bold text-[14px] text-slate-900">${c.user_name || ''}</span>
                                                        <div class="text-[14.5px] text-slate-800 leading-relaxed break-words w-full">
                                                            ${c.content || ''}
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 flex gap-3 font-bold text-[13px] text-slate-500">
                                                        <button class="hover:underline hover:text-slate-900 transition-colors">Phản hồi</button>
                                                        <span>·</span>
                                                        <span>${c.created_at || ''}</span>
                                                    </div>
                                                </div>`;
                                            repliesContainer.appendChild(replyHtml);
                                        } else {
                                            // if cannot find parent, fallback to prepend in comments list
                                            const list = document.getElementById(
                                                'comments-list');
                                            if (list) list.insertBefore(wrapper, list
                                                .firstChild);
                                        }
                                    }

                                    // update comments count if provided
                                    const commentsCountEl = document.getElementById(
                                        'comments-count');
                                    if (commentsCountEl && typeof data.comments_count !==
                                        'undefined') {
                                        commentsCountEl.textContent = data.comments_count +
                                            ' bình luận';
                                    }

                                    // clear the form input that was used
                                    const contentInput = this.querySelector('[name="content"]');
                                    if (contentInput) {
                                        contentInput.value = '';
                                        if (contentInput.tagName.toLowerCase() === 'textarea') {
                                            contentInput.style.height = '';
                                        }
                                    }

                                    // remove highlight after some time
                                    setTimeout(() => {
                                        wrapper.classList.remove('target-comment');
                                    }, 3000);

                                    return;
                                }

                                // if no structured comment comes back, fallback
                                window.location.reload();
                            } else {
                                if (data.message) alert(data.message);
                                else window.location.reload();
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            // On error, fallback to full submit to keep behavior consistent
                            this.submit();
                        })
                        .finally(() => {
                            if (submitBtn) submitBtn.disabled = false;
                        });
                });
            });

        });
    </script>
@endsection
