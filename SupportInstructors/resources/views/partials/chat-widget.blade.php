<div x-data="chatWidget()" class="fixed bottom-6 right-6 z-[100] font-sans">

    <button @click="isOpen = !isOpen"
        class="w-12 h-12 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-all duration-200 flex items-center justify-center relative">
        <span class="material-symbols-outlined text-[24px] absolute transition-all duration-300"
            :class="isOpen ? 'opacity-0 rotate-90 scale-50' : 'opacity-100 rotate-0 scale-100'">chat</span>
        <span class="material-symbols-outlined text-[26px] absolute transition-all duration-300"
            :class="isOpen ? 'opacity-100 rotate-0 scale-100' : 'opacity-0 -rotate-90 scale-50'">close</span>
    </button>

    <div x-show="isOpen" x-transition.opacity.duration.300ms x-cloak
        class="absolute bottom-16 right-0 w-[320px] sm:w-[350px] h-[500px] bg-white rounded-sm shadow-[0_5px_25px_-5px_rgba(0,0,0,0.2)] border border-slate-300 flex flex-col overflow-hidden">

        {{-- HEADER --}}
        <div class="bg-blue-600 text-white px-4 py-3 flex justify-between items-center z-10 shrink-0 shadow-sm relative">
            <div class="flex items-center gap-2 w-full">
                <button x-show="activeChat" @click="goBack()"
                    class="w-7 h-7 -ml-2 rounded hover:bg-white/20 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined !text-[18px]">arrow_back</span>
                </button>

                <div class="flex flex-col flex-1 min-w-0">
                    <h3 class="font-bold text-[14px] leading-tight truncate"
                        x-text="activeChat ? activeChat.name : 'Nhắn tin hỗ trợ'"></h3>
                    <p class="text-[11px] text-blue-100 font-medium truncate"
                        x-text="activeChat ? (activeChat.is_online ? 'Đang hoạt động' : 'Ngoại tuyến') : 'Chọn người liên hệ'">
                    </p>
                </div>
            </div>

            <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center"
                x-show="activeChat && activeChat.is_online">
                <div class="w-2.5 h-2.5 rounded-full bg-green-400 border border-blue-600"></div>
            </div>
        </div>

        {{-- CẢNH 1: DANH BẠ VÀ TÌM KIẾM --}}
        <div x-show="!activeChat" x-transition class="flex-1 overflow-y-auto bg-white custom-scrollbar flex flex-col">
            <div class="p-2 border-b border-slate-100 sticky top-0 bg-white/95 backdrop-blur-sm z-10 shrink-0">
                <div class="relative flex items-center w-full h-9 rounded-sm bg-slate-100 px-3">
                    <span class="material-symbols-outlined !text-[16px] text-slate-400 mr-2">search</span>
                    {{-- SỬA Ở ĐÂY: Thêm x-model cho tìm kiếm --}}
                    <input type="text" x-model="searchQuery" placeholder="Tìm người liên hệ..."
                        class="w-full bg-transparent border-none focus:ring-0 text-[12px] text-slate-700 placeholder:text-slate-400 p-0">
                </div>
            </div>

            <div x-show="isLoadingContacts" class="flex justify-center py-10 opacity-50">
                <span class="material-symbols-outlined animate-spin !text-[24px] text-blue-600">progress_activity</span>
            </div>

            <div x-show="!isLoadingContacts && filteredContacts.length === 0"
                class="flex flex-col items-center justify-center h-40 text-center px-6">
                <span class="material-symbols-outlined !text-[40px] text-slate-300 mb-2">search_off</span>
                <p class="text-[12px] text-slate-500">Không tìm thấy liên hệ.</p>
            </div>

            {{-- SỬA Ở ĐÂY: Lặp qua filteredContacts thay vì contacts --}}
            <div class="flex-1 overflow-y-auto custom-scrollbar">
                <template x-for="contact in filteredContacts" :key="contact.id">
                    <div @click="openChat(contact)"
                        class="flex items-center gap-3 p-3 border-b border-slate-50 hover:bg-slate-50 cursor-pointer transition-colors relative group">

                        <div class="relative shrink-0">
                            <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center font-bold text-[16px] text-blue-600 border border-blue-100 shadow-sm"
                                x-text="contact.name.charAt(0).toUpperCase()"></div>
                            <div x-show="contact.is_online"
                                class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full">
                            </div>
                        </div>

                        <div class="flex-1 min-w-0 pr-2">
                            <div class="flex justify-between items-center mb-0.5">
                                <p class="font-bold text-[13px] text-slate-800 truncate pr-2"
                                    :class="contact.unread_count > 0 ? 'text-black' : ''" x-text="contact.name"></p>
                                <div x-show="contact.unread_count > 0"
                                    class="shrink-0 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center"
                                    x-text="contact.unread_count"></div>
                            </div>

                            <div class="flex justify-between items-center gap-2">
                                <p class="text-[12px] text-slate-500 truncate"
                                    :class="contact.unread_count > 0 ? 'font-bold text-slate-800' : ''"
                                    x-text="contact.latest_message.includes('url') ? (contact.latest_message.includes('image') ? '[Hình ảnh]' : '[Tập tin đính kèm]') : contact.latest_message">
                                </p>
                                <span class="text-[10px] text-slate-400 shrink-0"
                                    :class="contact.unread_count > 0 ? 'font-bold text-blue-600' : ''"
                                    x-text="formatRelativeTime(contact.latest_message_time)"></span>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- CẢNH 2: KHUNG CHAT --}}
        <div x-show="activeChat" x-transition.opacity.duration.300ms
            class="flex-1 flex flex-col min-h-0 bg-slate-50/50 relative">

            <div x-show="isLoadingMessages"
                class="absolute inset-0 bg-white/80 backdrop-blur-sm z-20 flex flex-col items-center justify-center">
                <span
                    class="material-symbols-outlined animate-spin !text-[24px] text-blue-600 mb-2">progress_activity</span>
            </div>

            <div class="flex-1 min-h-0 overflow-y-auto p-3 space-y-1 custom-scrollbar flex flex-col relative"
                id="chat-messages-container" @scroll="checkScroll()">

                {{-- Nút Cuộn Xuống Dưới (Chỉ hiện khi đang xem tin cũ có tin mới) --}}
                <button x-show="showScrollButton" @click="scrollToBottom(true)"
                    class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 w-8 h-8 rounded-full border border-blue-500 text-blue-500 bg-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center hover:bg-blue-50 transition-colors">
                    <span class="material-symbols-outlined !text-[20px]">expand_more</span>

                    {{-- Chấm đỏ báo có tin mới (Tùy chọn cho sinh động) --}}
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full border border-white"></span>
                </button>
                <template x-for="(msg, index) in groupedMessages" :key="msg.id">
                    <div class="w-full flex flex-col"
                        :class="msg.sender_id == currentUserId ? 'items-end' : 'items-start'">

                        <div x-show="msg.showDateDivider" class="w-full flex justify-center my-4">
                            <span class="text-[10px] font-medium text-slate-400 bg-slate-100 px-2 py-0.5 rounded-sm"
                                x-text="formatDividerDate(msg.created_at)"></span>
                        </div>

                        <div class="flex items-center w-full relative group"
                            :class="msg.sender_id == currentUserId ? 'justify-end' : 'justify-start'">

                            {{-- Avatar người gửi (người kia) --}}
                            <div x-show="msg.sender_id != currentUserId" class="w-6 h-6 shrink-0 mr-1.5 flex items-end">
                                <div x-show="msg.isLastInGroup"
                                    class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold border border-white shadow-sm"
                                    x-text="activeChat?.name.charAt(0)"></div>
                            </div>

                            {{-- Nút Tùy chọn (Tin người kia gửi - Bên phải tin nhắn) --}}
                            <button x-show="msg.sender_id != currentUserId && activeMenuId !== msg.id"
                                @click="activeMenuId = msg.id"
                                class="text-slate-300 hover:text-slate-500 opacity-0 group-hover:opacity-100 p-1 order-last ml-1"
                                title="Tùy chọn">
                                <span class="material-symbols-outlined !text-[18px]">more_vert</span>
                            </button>

                            {{-- Nút Tùy chọn (Tin mình gửi - Bên trái tin nhắn) --}}
                            <button x-show="msg.sender_id == currentUserId && activeMenuId !== msg.id"
                                @click="activeMenuId = msg.id"
                                class="text-slate-300 hover:text-slate-500 opacity-0 group-hover:opacity-100 p-1 order-first mr-1"
                                title="Tùy chọn">
                                <span class="material-symbols-outlined !text-[18px]">more_vert</span>
                            </button>

                            <div :class="[
                                msg.sender_id == currentUserId ? 'bg-blue-600 text-white' :
                                'bg-white text-slate-700 border border-slate-200',
                                msg.sender_id == currentUserId ?
                                (msg.isFirstInGroup && msg.isLastInGroup ? 'rounded-2xl' :
                                    (msg.isFirstInGroup ? 'rounded-l-2xl rounded-tr-2xl rounded-br-sm' :
                                        (msg.isLastInGroup ? 'rounded-l-2xl rounded-tr-sm rounded-br-2xl' :
                                            'rounded-l-2xl rounded-r-sm'))) :
                                (msg.isFirstInGroup && msg.isLastInGroup ? 'rounded-2xl' :
                                    (msg.isFirstInGroup ? 'rounded-r-2xl rounded-tl-2xl rounded-bl-sm' :
                                        (msg.isLastInGroup ? 'rounded-r-2xl rounded-tl-sm rounded-bl-2xl' :
                                            'rounded-r-2xl rounded-l-sm')))
                            ]"
                                class="px-3 py-2 max-w-[70%] text-[13px] shadow-sm flex flex-col break-words transition-all relative">

                                <template x-if="msg.is_recalled">
                                    <span
                                        class="italic opacity-60 text-[12px] border border-dashed border-current px-2 py-0.5 rounded-sm">Tin
                                        nhắn đã thu hồi</span>
                                </template>

                                <template x-if="!msg.is_recalled">
                                    <div>
                                        <span x-show="msg.type === 'text'" x-text="msg.content"
                                            class="whitespace-pre-wrap leading-relaxed"></span>
                                        <template x-if="msg.type === 'image'">
                                            <img :src="parseFile(msg.content).url" @load="scrollToBottom(false)"
                                                class="w-full max-h-[200px] object-cover rounded-sm cursor-zoom-in border border-black/10 bg-white">
                                        </template>
                                        <template x-if="msg.type === 'file'">
                                            <a :href="parseFile(msg.content).url" target="_blank"
                                                class="flex items-center gap-2 p-1.5 rounded-sm bg-black/10 hover:bg-black/20 transition-colors mt-1">
                                                <span
                                                    class="material-symbols-outlined !text-[20px] shrink-0">description</span>
                                                <span class="truncate underline text-[12px]"
                                                    x-text="parseFile(msg.content).name"></span>
                                            </a>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Dòng MENU Tùy chọn (Xóa phía tôi / Thu hồi) --}}
                        <div x-show="activeMenuId === msg.id" x-transition.opacity x-cloak
                            class="flex items-center gap-1.5 mt-1 w-full"
                            :class="msg.sender_id == currentUserId ? 'justify-end pr-1' : 'justify-start pl-8'">

                            <button @click="executeDeleteForMe(msg.id)"
                                class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-sm hover:bg-slate-200 font-medium transition-colors">
                                Xóa phía tôi
                            </button>

                            {{-- Chỉ hiện Thu hồi nếu là tin mình gửi VÀ chưa quá 60 phút --}}
                            <button x-show="canRecall(msg)" @click="executeRecall(msg.id)"
                                class="text-[10px] px-2 py-0.5 bg-red-50 text-red-600 border border-red-200 rounded-sm hover:bg-red-500 hover:text-white font-medium transition-colors">
                                Thu hồi
                            </button>

                            <button @click="activeMenuId = null"
                                class="text-[10px] px-2 py-0.5 text-slate-400 hover:underline">Đóng</button>
                        </div>

                        {{-- 1. Nếu là tin nhắn của NGƯỜI KIA -> Hiện giờ ở cuối cụm --}}
                        <div x-show="msg.isLastInGroup && msg.sender_id != currentUserId && activeMenuId !== msg.id"
                            class="mt-0.5 mb-2 pl-9 flex items-center justify-start">
                            <span class="text-[9px] text-slate-400" x-text="formatTimeOnly(msg.created_at)"></span>
                        </div>

                        {{-- 2. Nếu là tin nhắn CỦA MÌNH -> Hiện giờ ở cuối cụm. Riêng Trạng thái thì chỉ hiện ở tin nhắn cuối cùng nhất --}}
                        <div x-show="msg.isLastInGroup && msg.sender_id == currentUserId && activeMenuId !== msg.id"
                            class="mt-0.5 mb-2 pr-1 flex items-center justify-end gap-1">

                            {{-- Giờ (Lúc nào cũng hiện ở cuối cụm tin nhắn) --}}
                            <span class="text-[9px] text-slate-400" x-text="formatTimeOnly(msg.created_at)"></span>

                            {{-- Trạng thái (Chỉ hiện cho tin nhắn cuối cùng nhất của bạn) --}}
                            <template x-if="msg.isLastOfMe">
                                <div class="flex items-center">
                                    {{-- Đã xem --}}
                                    <template x-if="msg.read_at">
                                        <span class="text-[10px] text-blue-500 flex items-center gap-0.5 ml-1">
                                            <span class="material-symbols-outlined !text-[12px]">done_all</span> Đã xem
                                        </span>
                                    </template>

                                    {{-- Chưa xem (Đã gửi / Đã nhận) --}}
                                    <template x-if="!msg.read_at">
                                        <span class="text-[10px] text-slate-400 flex items-center gap-0.5 ml-1">
                                            <template x-if="activeChat?.is_online">
                                                <span><span
                                                        class="material-symbols-outlined !text-[12px]">done_all</span>
                                                    Đã nhận</span>
                                            </template>
                                            <template x-if="!activeChat?.is_online">
                                                <span><span class="material-symbols-outlined !text-[12px]">check</span>
                                                    Đã gửi</span>
                                            </template>
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>

                    </div>
                </template>
            </div>

            {{-- Vùng nhập liệu (Đã khóa ô nhập khi chọn tệp & Sửa lỗi hiện 2 tệp) --}}
            <div class="p-2 bg-white border-t border-slate-200 shrink-0">

                {{-- Hiển thị file chuẩn bị gửi --}}
                <div x-show="selectedFile" x-cloak
                    class="mb-2 px-3 py-2 bg-blue-50 border border-blue-100 rounded-sm flex items-center justify-between text-[11px] text-blue-700 animate-fade-in-up">
                    <span class="truncate max-w-[220px] flex items-center gap-2">
                        <span class="material-symbols-outlined !text-[16px]"
                            x-text="selectedFile && selectedFile.type.startsWith('image/') ? 'image' : 'description'"></span>
                        <span class="font-medium" x-text="selectedFile ? selectedFile.name : ''"></span>
                    </span>
                    <button type="button" @click="selectedFile = null; $refs.fileInput.value = ''"
                        class="text-slate-400 hover:text-red-500 transition-colors px-1">
                        <span class="material-symbols-outlined !text-[16px]">close</span>
                    </button>
                </div>

                <form @submit.prevent="sendMessage()" class="flex items-center gap-2 relative">
                    <input type="file" x-ref="fileInput" class="hidden"
                        @change="selectedFile = $refs.fileInput.files[0]; $refs.chatInput.focus()">

                    {{-- Nút Đính kèm --}}
                    <button type="button" @click="$refs.fileInput.click()"
                        class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-sm transition-all shrink-0">
                        <span class="material-symbols-outlined !text-[22px]">attach_file</span>
                    </button>

                    {{-- Ô nhập tin nhắn: Tự động khóa (:disabled) và đổi màu nền khi chọn tệp --}}
                    <div class="flex-1">
                        <textarea x-ref="chatInput" x-model="newMessage" @keydown.enter.prevent="if(!event.shiftKey) sendMessage()"
                            :disabled="selectedFile" :placeholder="selectedFile ? 'Đã chọn tệp...' : 'Aa...'"
                            :class="selectedFile ? 'bg-slate-200 cursor-not-allowed opacity-70 border-slate-300' :
                                'bg-slate-50 border-slate-200'"
                            class="w-full border rounded-sm pl-3 pr-3 py-[9px] text-[13px] text-slate-700 focus:ring-0 focus:border-blue-400 focus:bg-white resize-none overflow-hidden max-h-[100px] min-h-[40px] custom-scrollbar block transition-all shadow-inner leading-normal"
                            rows="1" @input="resizeTextarea($event.target)"></textarea>
                    </div>

                    {{-- Nút Gửi --}}
                    <button type="submit"
                        class="w-10 h-10 bg-blue-600 text-white rounded-sm flex items-center justify-center shadow-sm transition-all shrink-0"
                        :class="(!newMessage.trim() && !selectedFile) ? 'opacity-40 cursor-not-allowed grayscale' :
                        'hover:bg-blue-700 active:scale-95'">
                        <span class="material-symbols-outlined !text-[20px] ml-0.5">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<style>
    /* Ẩn scrollbar cho Chrome, Safari và Opera */
    .custom-scrollbar::-webkit-scrollbar {
        display: none;
    }

    /* Ẩn scrollbar cho IE, Edge và Firefox */
    .custom-scrollbar {
        -ms-overflow-style: none;
        /* IE and Edge */
        scrollbar-width: none;
        /* Firefox */
    }
</style>
<script>
    function chatWidget() {
        return {
            isOpen: false,
            activeChat: null,
            contacts: [],
            searchQuery: '', // Biến tìm kiếm
            messages: [],
            newMessage: '',
            selectedFile: null,
            currentConversationId: null,
            isLoadingContacts: true,
            isLoadingMessages: false,
            activeMenuId: null, // Dùng để mở Menu Hành động (Xóa/Thu hồi)
            pollInterval: null,
            isUserScrollingUp: false,
            showScrollButton: false,

            currentUserId: {{ auth()->id() }},

            init() {
                this.startPollingContacts();
                this.$watch('isOpen', value => {
                    if (value) {
                        if (this.activeChat) {
                            this.scrollToBottom(true);
                            this.startPollingMessages();
                        } else this.startPollingContacts();
                    } else this.stopPolling();
                });
            },

            // LỌC DANH BẠ THEO TỪ KHÓA
            get filteredContacts() {
                if (this.searchQuery.trim() === '') return this.contacts;

                // Chuyển từ khóa tìm kiếm về chữ thường và xóa dấu
                let query = this.removeAccents(this.searchQuery.toLowerCase());

                return this.contacts.filter(c => {
                    // Chuyển tên người dùng về chữ thường và xóa dấu để so sánh
                    let normalizedName = this.removeAccents(c.name.toLowerCase());
                    return normalizedName.includes(query);
                });
            },

            // Hàm chuyển đổi Tiếng Việt có dấu thành không dấu
            removeAccents(str) {
                return str.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd').replace(/Đ/g, 'D');
            },

            // === GOM NHÓM VÀ TÍNH TOÁN TRẠNG THÁI ===
            get groupedMessages() {
                let grouped = [];
                let myLastMsgIndex = -1;

                for (let i = 0; i < this.messages.length; i++) {
                    let msg = {
                        ...this.messages[i]
                    };
                    let prevMsg = i > 0 ? this.messages[i - 1] : null;
                    let nextMsg = i < this.messages.length - 1 ? this.messages[i + 1] : null;

                    let msgDate = new Date(msg.created_at);
                    let prevDate = prevMsg ? new Date(prevMsg.created_at) : null;

                    // Cắt ngang ngày (hoặc cách nhau > 10 phút = 600,000 ms)
                    msg.showDateDivider = !prevDate || (msgDate - prevDate) > 600000;

                    // Nhóm tin nhắn (Điều kiện: cùng người gửi, cách nhau < 1 phút, và không bị chia cắt bởi dải thời gian)
                    let isSameSenderAsPrev = prevMsg && prevMsg.sender_id == msg.sender_id;
                    let isCloseToPrev = prevDate && (msgDate - prevDate) < 60000 && !msg.showDateDivider;
                    msg.isFirstInGroup = !(isSameSenderAsPrev && isCloseToPrev);

                    let isSameSenderAsNext = nextMsg && nextMsg.sender_id == msg.sender_id;
                    let isCloseToNext = nextMsg && (new Date(nextMsg.created_at) - msgDate) < 60000;

                    // Kiểm tra xem tin nhắn tiếp theo có bị cắt bởi dải thời gian không (cũng là 10 phút)
                    let nextMsgHasDivider = nextMsg && (new Date(nextMsg.created_at) - msgDate) > 600000;
                    msg.isLastInGroup = !(isSameSenderAsNext && isCloseToNext && !nextMsgHasDivider);

                    // Lấy vị trí tin nhắn cuối cùng của mình
                    if (msg.sender_id == this.currentUserId) myLastMsgIndex = i;

                    grouped.push(msg);
                }

                if (myLastMsgIndex !== -1) {
                    grouped[myLastMsgIndex].isLastOfMe = true;
                }

                return grouped;
            },

            checkScroll() {
                let c = document.getElementById('chat-messages-container');
                if (!c) return;
                this.isUserScrollingUp = c.scrollHeight - c.scrollTop - c.clientHeight > 150;
                if (!this.isUserScrollingUp) this.showScrollButton = false;
            },

            stopPolling() {
                if (this.pollInterval) {
                    clearInterval(this.pollInterval);
                    this.pollInterval = null;
                }
            },

            startPollingContacts() {
                this.stopPolling();
                if (this.contacts.length === 0) this.fetchContacts();
                this.pollInterval = setInterval(() => {
                    if (this.isOpen && !this.activeChat) this.fetchContactsSilent();
                }, 3000);
            },

            startPollingMessages() {
                this.stopPolling();
                this.pollInterval = setInterval(() => {
                    if (this.isOpen && this.activeChat) {
                        this.fetchMessagesSilent();
                        this.fetchContactsSilent();
                    }
                }, 2000);
            },

            fetchContactsSilent() {
                fetch('/chat/contacts')
                    .then(res => res.json())
                    .then(data => {
                        this.contacts = data;
                        if (this.activeChat) {
                            let updatedContact = this.contacts.find(c => c.id == this.activeChat.id);
                            if (updatedContact) this.activeChat.is_online = updatedContact.is_online;
                        }
                    });
            },

            fetchMessagesSilent() {
                if (!this.activeChat) return;
                fetch(`/chat/messages/${this.activeChat.id}`)
                    .then(res => res.json())
                    .then(data => {
                        let oldLength = this.messages.length;
                        this.messages = data.messages;

                        if (data.messages.length > oldLength) {
                            if (this.isUserScrollingUp) {
                                this.showScrollButton = true;
                            } else {
                                this.scrollToBottom(true);
                            }
                        }
                    });
            },

            fetchContacts() {
                this.isLoadingContacts = true;
                fetch('/chat/contacts')
                    .then(res => res.json())
                    .then(data => {
                        this.contacts = data;
                    })
                    .finally(() => {
                        this.isLoadingContacts = false;
                    });
            },

            openChat(contact) {
                this.activeChat = contact;
                this.messages = [];
                this.activeMenuId = null;
                this.searchQuery = ''; // Xóa chữ tìm kiếm khi mở chat
                this.isUserScrollingUp = false;
                this.showScrollButton = false;
                this.isLoadingMessages = true;
                fetch(`/chat/messages/${contact.id}`)
                    .then(res => res.json())
                    .then(data => {
                        this.currentConversationId = data.conversation_id;
                        this.messages = data.messages;
                        this.scrollToBottom(true);
                        this.startPollingMessages();
                    })
                    .finally(() => {
                        this.isLoadingMessages = false;
                    });
            },

            goBack() {
                this.activeChat = null;
                this.activeMenuId = null;
                this.startPollingContacts();
            },

            sendMessage() {
                if (!this.newMessage.trim() && !this.selectedFile) return;

                let formData = new FormData();
                formData.append('conversation_id', this.currentConversationId);
                formData.append('content', this.newMessage.trim());

                let tempMsg = {
                    id: 'temp_' + Date.now(),
                    sender_id: this.currentUserId,
                    content: this.newMessage.trim() || 'Đang gửi file...',
                    type: 'text',
                    created_at: new Date().toISOString(),
                    read_at: null
                };

                if (this.selectedFile) {
                    formData.append('attachment', this.selectedFile);
                    tempMsg.content = 'Đang tải tệp...';
                }

                this.messages.push(tempMsg);
                this.newMessage = '';
                this.selectedFile = null;
                this.activeMenuId = null;
                this.$refs.fileInput.value = '';
                this.$refs.chatInput.style.height = 'auto';

                this.isUserScrollingUp = false;
                this.showScrollButton = false;
                this.scrollToBottom(true);

                fetch('/chat/send', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: formData
                    }).then(res => res.json())
                    .then(data => {
                        let index = this.messages.findIndex(m => m.id === tempMsg.id);
                        if (index !== -1) this.messages[index] = data;
                        this.scrollToBottom(true);
                    });
            },

            // Kiểm tra xem tin nhắn có đủ điều kiện thu hồi không
            canRecall(msg) {
                if (msg.sender_id != this.currentUserId) return false;
                if (msg.is_recalled) return false;
                const diffMins = (new Date() - new Date(msg.created_at)) / 60000;
                return diffMins <= 60; // Chỉ cho phép thu hồi trong vòng 60 phút
            },

            executeRecall(id) {
                fetch(`/chat/recall/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            let msg = this.messages.find(m => m.id === id);
                            if (msg) msg.is_recalled = true;
                        } else if (data.message) {
                            alert(data.message); // Báo lỗi nếu quá hạn 60p
                        }
                        this.activeMenuId = null;
                    }).catch(() => {
                        this.activeMenuId = null;
                    });
            },

            executeDeleteForMe(id) {
                fetch(`/chat/delete-for-me/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    }).then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Ẩn tin nhắn khỏi giao diện lập tức
                            this.messages = this.messages.filter(m => m.id !== id);
                        }
                        this.activeMenuId = null;
                    }).catch(() => {
                        this.activeMenuId = null;
                    });
            },

            parseFile(jsonStr) {
                try {
                    return JSON.parse(jsonStr);
                } catch (e) {
                    return {
                        url: '',
                        name: 'Lỗi file'
                    };
                }
            },

            scrollToBottom(force = true) {
                setTimeout(() => {
                    let container = document.getElementById('chat-messages-container');
                    if (container) {
                        if (force || !this.isUserScrollingUp) {
                            container.scrollTop = container.scrollHeight;
                            this.showScrollButton = false;
                        }
                    }
                }, 50);
            },

            resizeTextarea(el) {
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight) + 'px';
            },

            getRoleName(roleId) {
                if (roleId === 1) return 'Quản trị viên';
                if (roleId === 2) return 'Cố vấn học tập';
                return 'Sinh viên';
            },

            formatDividerDate(dateString) {
                const date = new Date(dateString);
                const now = new Date();

                const timeStr = date.toLocaleTimeString('vi-VN', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                if (date.toDateString() === now.toDateString()) {
                    return timeStr;
                }

                const yesterday = new Date(now);
                yesterday.setDate(now.getDate() - 1);
                if (date.toDateString() === yesterday.toDateString()) {
                    return 'Hôm qua ' + timeStr;
                }

                // Lấy Thứ (T2, T3...)
                const days = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                const dayName = days[date.getDay()];

                return dayName + ', ' + date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                }) + ' ' + timeStr;
            },

            formatTimeOnly(dateString) {
                return new Date(dateString).toLocaleTimeString('vi-VN', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            formatRelativeTime(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                const diffSeconds = Math.floor((new Date() - date) / 1000);

                if (diffSeconds < 60) return 'Vừa xong';
                if (diffSeconds < 3600) return Math.floor(diffSeconds / 60) + ' phút';
                if (diffSeconds < 86400) return Math.floor(diffSeconds / 3600) + ' giờ';
                return date.toLocaleDateString('vi-VN', {
                    day: '2-digit',
                    month: '2-digit'
                });
            }
        }
    }
</script>
