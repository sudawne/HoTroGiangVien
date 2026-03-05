@extends('layouts.admin')
@section('title', $notification->title)

@section('content')
    <div class="w-full px-4 py-6 max-w-4xl mx-auto">
        <div class="mb-4 flex justify-between items-center">
            <a href="{{ route('admin.notifications.index') }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-slate-500 hover:text-primary transition-colors">
                <span class="material-symbols-outlined !text-[18px]">arrow_back</span> Quay lại danh sách
            </a>

            @if ($notification->status == 'pending' && Auth::user()->role_id == 1)
                <form id="approve-form-show" action="{{ route('admin.notifications.approve', $notification->id) }}"
                    method="POST" class="hidden">
                    @csrf
                </form>
                <button type="button"
                    onclick="showConfirm('Duyệt bài', 'Hệ thống sẽ ngay lập tức xuất bản thông báo và GỬI EMAIL tự động đến sinh viên. Bạn có chắc chắn?', () => document.getElementById('approve-form-show').submit(), 'primary')"
                    class="px-4 py-2 bg-green-600 text-white rounded-sm text-sm font-bold hover:bg-green-700 flex items-center gap-2 shadow-sm transition-colors">
                    <span class="material-symbols-outlined !text-[18px]">check_circle</span> Duyệt & Gửi Email
                </button>
            @endif
        </div>

        <div class="bg-white border border-slate-200 rounded-sm shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-6 border-b border-slate-100 relative overflow-hidden">
                <span
                    class="material-symbols-outlined absolute -right-10 -top-10 text-[150px] text-slate-50 opacity-50 pointer-events-none select-none">campaign</span>

                <div class="relative z-10">
                    <div class="flex items-center gap-3 mb-4">
                        @if ($notification->target_audience == 'all')
                            <span
                                class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold uppercase rounded border border-slate-200">Gửi
                                Toàn trường</span>
                        @else
                            <span
                                class="px-2.5 py-1 bg-green-50 text-green-700 text-xs font-bold uppercase rounded border border-green-200">Gửi
                                riêng Lớp: {{ $notification->class->code ?? 'N/A' }}</span>
                        @endif

                        @if ($notification->status == 'draft')
                            <span
                                class="px-2.5 py-1 bg-slate-100 text-slate-600 text-xs font-bold uppercase rounded border border-slate-200">Bản
                                nháp</span>
                        @elseif($notification->status == 'pending')
                            <span
                                class="px-2.5 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold uppercase rounded border border-yellow-200 flex items-center gap-1">
                                <span class="material-symbols-outlined !text-[14px]">hourglass_empty</span> Chờ duyệt
                            </span>
                        @endif
                    </div>

                    <h1 class="text-3xl font-bold text-slate-800 leading-tight mb-4">
                        {{ $notification->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-6 text-sm text-slate-500">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                {{ substr($notification->sender->name ?? 'A', 0, 1) }}
                            </div>
                            <span>Bởi <strong
                                    class="text-slate-700">{{ $notification->sender->name ?? 'Hệ thống' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <span class="material-symbols-outlined !text-[18px]">calendar_month</span>
                            <span>{{ $notification->created_at->format('d/m/Y - H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-6 py-8 prose prose-slate prose-a:text-blue-600 max-w-none text-slate-700"
                style="word-wrap: break-word;">
                {!! $notification->message !!}
            </div>

            @if ($notification->attachment_url)
                <div
                    class="px-6 py-5 bg-slate-50 border-t border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-lg shadow-inner">
                            <span class="material-symbols-outlined !text-[28px]">description</span>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-0.5">Tài liệu đính kèm
                            </p>
                            <p class="text-sm font-bold text-slate-800 line-clamp-1">{{ $notification->attachment_name }}
                            </p>
                        </div>
                    </div>
                    <a href="{{ asset('storage/' . $notification->attachment_url) }}"
                        download="{{ $notification->attachment_name }}"
                        class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-bold rounded-sm hover:bg-slate-100 hover:text-blue-600 transition-colors shadow-sm flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined !text-[20px]">download</span> Tải Xuống
                    </a>
                </div>
            @endif

            <div class="px-6 py-6 bg-white">
                <div class="flex items-center gap-6 mb-6 pb-4 border-b border-slate-100">
                    <form action="{{ route('admin.notifications.like', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="flex items-center gap-2 px-4 py-2 rounded-full border transition-all duration-300 {{ $notification->isLikedBy(Auth::id()) ? 'bg-red-50 border-red-200 text-red-500 hover:bg-red-100' : 'bg-white border-slate-300 text-slate-600 hover:bg-slate-50' }}">
                            <span class="material-symbols-outlined !text-[20px] transition-transform active:scale-125"
                                {{ $notification->isLikedBy(Auth::id()) ? 'style=font-variation-settings:"FILL"1' : '' }}>favorite</span>
                            <span class="text-sm font-bold">{{ $notification->likes_count }} Thích</span>
                        </button>
                    </form>
                    <div class="flex items-center gap-2 text-slate-600 font-bold text-sm">
                        <span class="material-symbols-outlined !text-[20px]">chat</span>
                        <span>{{ $notification->comments_count }} Bình luận</span>
                    </div>
                </div>

                @if ($notification->allow_comments)
                    <form action="{{ route('admin.notifications.comment', $notification->id) }}" method="POST"
                        class="mb-8 flex gap-3 items-start">
                        @csrf
                        <div
                            class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-sm flex-shrink-0 shadow-sm mt-1">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 relative">
                            <textarea name="content" rows="2" required placeholder="Thảo luận, đặt câu hỏi về thông báo này..."
                                class="w-full pl-4 pr-14 py-3 border border-slate-300 rounded-lg text-sm focus:ring-1 focus:ring-primary focus:border-primary resize-none"></textarea>
                            <button type="submit"
                                class="absolute right-3 bottom-3 p-1.5 bg-primary text-white rounded hover:bg-primary/90 transition-colors shadow-sm"
                                title="Gửi bình luận">
                                <span class="material-symbols-outlined !text-[18px]">send</span>
                            </button>
                        </div>
                    </form>

                    <div class="space-y-5">
                        @forelse($notification->comments as $comment)
                            <div class="flex gap-3">
                                <div
                                    class="w-9 h-9 rounded-full bg-slate-200 flex items-center justify-center text-slate-700 font-bold text-xs flex-shrink-0 border border-slate-300">
                                    {{ substr($comment->user->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <div
                                        class="bg-slate-50 border border-slate-200 px-4 py-3 rounded-2xl rounded-tl-none inline-block max-w-full">
                                        <h5 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                                            {{ $comment->user->name }}
                                            @if ($comment->user->role_id == 1)
                                                <span
                                                    class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[9px] uppercase rounded">Admin</span>
                                            @elseif($comment->user->role_id == 2)
                                                <span
                                                    class="px-1.5 py-0.5 bg-green-100 text-green-700 text-[9px] uppercase rounded">Giảng
                                                    viên</span>
                                            @endif
                                        </h5>
                                        <p class="text-sm text-slate-700 mt-1 whitespace-pre-wrap">{{ $comment->content }}
                                        </p>
                                    </div>
                                    <p class="text-[11px] text-slate-400 mt-1.5 ml-2">
                                        {{ $comment->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-center text-slate-500 text-sm italic py-4">Chưa có bình luận nào. Hãy là người
                                đầu
                                tiên thảo luận!</p>
                        @endforelse
                    </div>
                @else
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-lg text-center mt-4">
                        <span
                            class="material-symbols-outlined text-slate-400 !text-[24px] mb-1 block">comments_disabled</span>
                        <p class="text-sm font-medium text-slate-500">Tính năng bình luận đã bị tắt cho bài đăng này.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('admin.classes.partials.universal_confirm_modal')

    <script>
        function showConfirm(title, message, callback, type = 'primary') {
            const modal = document.getElementById('universalModal');
            document.getElementById('uni-modal-title').innerText = title;
            document.getElementById('uni-modal-desc').innerHTML = message;
            const btnConfirm = document.getElementById('btn-uni-confirm');

            btnConfirm.onclick = () => {
                modal.classList.add('hidden');
                callback();
            };

            const icon = document.getElementById('uni-modal-icon');
            const iconBg = document.getElementById('uni-modal-icon-bg');

            if (type === 'danger') {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-red-600 hover:bg-red-700 flex items-center gap-2";
                icon.innerText = 'warning';
                icon.className = 'material-symbols-outlined text-[24px] text-red-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-red-100';
            } else {
                btnConfirm.className =
                    "px-4 py-2 text-white font-medium rounded-sm text-sm bg-blue-600 hover:bg-blue-700 flex items-center gap-2";
                icon.innerText = 'help';
                icon.className = 'material-symbols-outlined text-[24px] text-blue-600';
                iconBg.className = 'flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100';
            }

            modal.classList.remove('hidden');
            modal.removeAttribute('style');
        }
    </script>
@endsection
