@extends('layouts.student') {{-- Đảm bảo bạn có layout này, hoặc đổi thành layout tương ứng --}}
@section('title', 'Bảng tin')

@section('styles')
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#463acb'
                    }
                }
            }
        }
    </script>
    <style>
        /* Ẩn scrollbar nhưng vẫn vuốt được */
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
    <div class="w-full min-h-screen bg-[#f0f2f5] pt-6 pb-20">

        {{-- CONTAINER CHÍNH GIỐNG FACEBOOK (Rộng vừa phải, canh giữa) --}}
        <div class="max-w-[680px] mx-auto px-4">

            {{-- HEADER BẢNG TIN --}}
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Bảng tin của bạn</h1>
            </div>

            {{-- DANH SÁCH BÀI ĐĂNG --}}
            <div class="space-y-6">
                @forelse($notifications as $notify)
                    {{-- Dùng Alpine.js để quản lý trạng thái mở/đóng comment cho từng bài --}}
                    <div x-data="{ showComments: false }"
                        class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">

                        {{-- 1. HEADER BÀI VIẾT (Người đăng + Thời gian) --}}
                        <div class="p-4 flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-base text-white shadow-sm {{ $notify->sender->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600' }}">
                                    {{ substr($notify->sender->name ?? 'A', 0, 1) }}
                                </div>
                                <div>
                                    <h3
                                        class="font-bold text-slate-800 text-[15px] leading-none hover:underline cursor-pointer">
                                        {{ $notify->sender->name ?? 'Hệ thống' }}
                                    </h3>
                                    <div class="flex items-center gap-1.5 text-[13px] text-slate-500 mt-1">
                                        <span>{{ $notify->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        @if ($notify->target_audience == 'all')
                                            <span class="flex items-center gap-0.5 font-semibold text-blue-600"
                                                title="Đã gửi cho Toàn trường">
                                                <span class="material-symbols-outlined !text-[14px]">public</span> Toàn
                                                trường
                                            </span>
                                        @else
                                            <span class="flex items-center gap-0.5 font-semibold text-emerald-600"
                                                title="Đã gửi cho Lớp của bạn">
                                                <span class="material-symbols-outlined !text-[14px]">group</span> Lớp bạn
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Badge Mức độ ưu tiên --}}
                            @if ($notify->type == 'urgent')
                                <span
                                    class="px-2 py-1 bg-red-50 text-red-600 text-[11px] font-bold uppercase rounded-md border border-red-100 flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-[14px]">warning</span> Khẩn
                                </span>
                            @elseif($notify->type == 'warning')
                                <span
                                    class="px-2 py-1 bg-orange-50 text-orange-600 text-[11px] font-bold uppercase rounded-md border border-orange-100 flex items-center gap-1">
                                    <span class="material-symbols-outlined !text-[14px]">error</span> Chú ý
                                </span>
                            @endif
                        </div>

                        {{-- 2. NỘI DUNG BÀI VIẾT --}}
                        <div class="px-4 pb-2">
                            <h2 class="text-lg font-bold text-slate-800 mb-2">{{ $notify->title }}</h2>
                            <div class="prose prose-slate max-w-none text-[15px] text-slate-700 leading-relaxed prose-a:text-blue-600 hover:prose-a:underline line-clamp-6"
                                style="word-wrap: break-word;">
                                {!! $notify->message !!}
                            </div>
                            {{-- Nút xem thêm nếu nội dung quá dài (chỉ là UI giả lập, có thể phát triển thêm) --}}
                            {{-- <button class="text-slate-500 font-bold hover:underline mt-1 text-[15px]">Xem thêm</button> --}}
                        </div>

                        {{-- 3. TÀI LIỆU ĐÍNH KÈM --}}
                        @if ($notify->attachment_url)
                            <div class="mx-4 my-3">
                                <a href="{{ asset('storage/' . $notify->attachment_url) }}"
                                    download="{{ $notify->attachment_name }}"
                                    class="flex items-center gap-3 p-3 bg-slate-50 border border-slate-200 rounded-lg hover:bg-slate-100 transition-colors group">
                                    <div
                                        class="p-2 bg-white rounded-md shadow-sm text-primary group-hover:scale-105 transition-transform">
                                        <span class="material-symbols-outlined !text-[24px]">description</span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-700 truncate">{{ $notify->attachment_name }}
                                        </p>
                                        <p class="text-[12px] text-slate-500">Nhấn để tải xuống</p>
                                    </div>
                                    <span
                                        class="material-symbols-outlined text-slate-400 group-hover:text-primary">download</span>
                                </a>
                            </div>
                        @endif

                        {{-- 4. THỐNG KÊ TƯƠNG TÁC (Like, Comment Count) --}}
                        <div
                            class="px-4 py-2 mx-4 border-b border-slate-100 flex justify-between items-center text-[13px] text-slate-500">
                            <div class="flex items-center gap-1 cursor-pointer hover:underline">
                                <span class="material-symbols-outlined !text-[16px] text-red-500"
                                    style="font-variation-settings:'FILL'1">favorite</span>
                                {{ $notify->likes_count }}
                            </div>
                            <div class="flex gap-3">
                                <span class="cursor-pointer hover:underline"
                                    @click="showComments = !showComments">{{ $notify->comments_count }} bình luận</span>
                            </div>
                        </div>

                        {{-- 5. NÚT HÀNH ĐỘNG (Like, Comment) --}}
                        <div class="px-4 py-1 flex items-center justify-between gap-1">
                            <form action="{{ route('student.notifications.like', $notify->id) }}" method="POST"
                                class="flex-1">
                                @csrf
                                <button type="submit"
                                    class="w-full py-2 flex items-center justify-center gap-2 rounded-md hover:bg-slate-50 transition-colors font-semibold text-[14px] {{ $notify->isLikedBy(Auth::id()) ? 'text-red-500' : 'text-slate-600' }}">
                                    <span
                                        class="material-symbols-outlined !text-[20px] transition-transform active:scale-125"
                                        {{ $notify->isLikedBy(Auth::id()) ? 'style=font-variation-settings:"FILL"1' : '' }}>favorite</span>
                                    Thích
                                </button>
                            </form>

                            <button @click="showComments = !showComments"
                                class="flex-1 py-2 flex items-center justify-center gap-2 rounded-md hover:bg-slate-50 transition-colors font-semibold text-[14px] text-slate-600">
                                <span class="material-symbols-outlined !text-[20px]">chat_bubble</span>
                                Bình luận
                            </button>
                        </div>

                        {{-- 6. KHU VỰC COMMENT (Ẩn/Hiện bằng Alpine.js) --}}
                        <div x-show="showComments" x-transition class="border-t border-slate-100 bg-slate-50/50 p-4">

                            {{-- Danh sách comment --}}
                            <div class="space-y-4 mb-4 max-h-[300px] overflow-y-auto hide-scroll">
                                @forelse($notify->comments as $comment)
                                    <div class="flex gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-white font-bold text-xs flex-shrink-0 shadow-sm {{ $comment->user->role_id == 3 ? 'bg-slate-600' : ($comment->user->role_id == 1 ? 'bg-blue-600' : 'bg-emerald-600') }}">
                                            {{ substr($comment->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div
                                                class="bg-white border border-slate-200 px-3 py-2 rounded-2xl rounded-tl-none inline-block max-w-full shadow-sm">
                                                <div class="flex items-center gap-2 mb-0.5">
                                                    <h5
                                                        class="text-[13px] font-bold text-slate-800 hover:underline cursor-pointer">
                                                        {{ $comment->user->name }}</h5>
                                                    @if ($comment->user->role_id == 1)
                                                        <span
                                                            class="px-1.5 py-[1px] bg-blue-100 text-blue-700 text-[9px] uppercase rounded font-bold">Admin</span>
                                                    @elseif($comment->user->role_id == 2)
                                                        <span
                                                            class="px-1.5 py-[1px] bg-emerald-100 text-emerald-700 text-[9px] uppercase rounded font-bold">Cố
                                                            vấn</span>
                                                    @endif
                                                </div>
                                                <p class="text-[14px] text-slate-700 whitespace-pre-wrap">
                                                    {{ $comment->content }}</p>
                                            </div>
                                            <div class="text-[11px] text-slate-500 mt-1 ml-2 font-medium">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-center text-slate-500 text-sm py-2">Chưa có bình luận nào.</p>
                                @endforelse
                            </div>

                            {{-- Ô nhập comment --}}
                            @if ($notify->allow_comments)
                                <form action="{{ route('student.notifications.comment', $notify->id) }}" method="POST"
                                    class="flex gap-2 items-end">
                                    @csrf
                                    <div
                                        class="w-8 h-8 rounded-full bg-slate-800 flex items-center justify-center text-white font-bold text-xs flex-shrink-0 mt-1 shadow-sm">
                                        {{ substr(Auth::user()->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 relative">
                                        <textarea name="content" rows="1" required placeholder="Viết bình luận..."
                                            oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"
                                            class="w-full pl-4 pr-12 py-2.5 bg-white border border-slate-300 rounded-2xl text-[14px] focus:ring-1 focus:ring-primary focus:border-primary resize-none shadow-sm overflow-hidden"
                                            style="min-height: 42px; max-height: 120px;"></textarea>
                                        <button type="submit"
                                            class="absolute right-2 bottom-1.5 p-1.5 text-primary hover:bg-primary/10 rounded-full transition-colors"
                                            title="Gửi">
                                            <span class="material-symbols-outlined !text-[20px]">send</span>
                                        </button>
                                    </div>
                                </form>
                            @else
                                <div
                                    class="text-center text-[13px] text-slate-500 bg-slate-100 py-2 rounded-lg border border-slate-200">
                                    Người đăng đã tắt tính năng bình luận cho bài viết này.
                                </div>
                            @endif

                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-12 text-center">
                        <span
                            class="material-symbols-outlined text-[60px] text-slate-300 mb-4 block">notifications_off</span>
                        <h3 class="text-lg font-bold text-slate-700">Chưa có thông báo nào</h3>
                        <p class="text-slate-500 mt-2">Khi nhà trường hoặc giảng viên đăng thông báo mới, chúng sẽ xuất hiện
                            tại đây.</p>
                    </div>
                @endforelse
            </div>

            {{-- Phân trang --}}
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>

        </div>
    </div>
@endsection
