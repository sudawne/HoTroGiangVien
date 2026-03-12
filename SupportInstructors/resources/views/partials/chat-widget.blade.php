<div x-data="chatWidget()" class="fixed bottom-6 right-6 z-[100] font-sans">

    {{-- Nút mở Chat --}}
    <button x-show="!isOpen" @click="isOpen = true" x-transition:leave="transition ease-in duration-150 transform"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-75"
        class="absolute bottom-0 right-0 w-14 h-14 bg-blue-600 text-white rounded-full shadow-[0_4px_20px_rgba(37,99,235,0.4)] hover:shadow-[0_6px_25px_rgba(37,99,235,0.6)] hover:-translate-y-1 hover:scale-105 active:scale-95 transition-all duration-300 ease-out flex items-center justify-center origin-center">
        <span class="material-symbols-outlined text-[28px]">chat</span>
    </button>

    {{-- Khung Chat --}}
    <div x-show="isOpen" @click.outside="isOpen = false"
        x-transition:enter="transition ease-[cubic-bezier(0.34,1.56,0.64,1)] duration-400 transform origin-bottom-right"
        x-transition:enter-start="opacity-0 scale-50" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200 transform origin-bottom-right"
        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-50" x-cloak
        class="absolute bottom-0 right-0 w-[320px] sm:w-[350px] h-[500px] bg-white rounded-xl shadow-[0_10px_40px_-10px_rgba(0,0,0,0.3)] border border-slate-200 flex flex-col overflow-hidden origin-bottom-right">

        {{-- HEADER --}}
        <div
            class="bg-blue-600 text-white px-4 py-3 flex justify-between items-center z-20 shrink-0 shadow-md relative">
            <div class="flex items-center gap-3 w-full">
                <button x-show="activeChat" @click="goBack()"
                    class="w-8 h-8 -ml-2 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined !text-[20px]">arrow_back</span>
                </button>

                {{-- Thông tin người dùng (Khi đang chat) --}}
                <div x-show="activeChat" class="flex items-center gap-2 flex-1 min-w-0" style="display: none;">
                    <div class="relative shrink-0">
                        <div class="w-9 h-9 rounded-full bg-white text-blue-600 flex items-center justify-center font-bold text-[14px] shadow-inner"
                            x-text="activeChat ? activeChat.name.charAt(0).toUpperCase() : ''"></div>
                        <div x-show="activeChat?.is_online"
                            class="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-400 border-2 border-blue-600 rounded-full">
                        </div>
                    </div>
                    <div class="flex flex-col min-w-0">
                        <h3 class="font-bold text-[14px] leading-tight truncate" x-text="activeChat?.name"></h3>
                        <p class="text-[11px] text-blue-100 font-medium truncate"
                            x-text="activeChat?.is_online ? 'Đang hoạt động' : getRoleName(activeChat?.role_id)"></p>
                    </div>
                </div>

                {{-- Thông tin (Khi ở danh bạ) --}}
                <div x-show="!activeChat" class="flex flex-col flex-1 min-w-0">
                    <h3 class="font-bold text-[15px] leading-tight">Nhắn tin hỗ trợ</h3>
                    <p class="text-[11px] text-blue-100 font-medium">Chọn người liên hệ</p>
                </div>
            </div>

            <div class="flex items-center gap-1">
                {{-- Nút Gợi Ý Tin Nhắn (Chỉ hiện cho Sinh viên) --}}
                @if (auth()->user()->hasRole('STUDENT'))
                    <div class="relative" x-show="activeChat" style="display: none;">
                        <button @click="showSuggestions = !showSuggestions" @click.outside="showSuggestions = false"
                            class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined !text-[20px]">help_outline</span>
                        </button>

                        <div x-show="showSuggestions" x-transition x-cloak
                            class="absolute top-10 right-0 w-64 bg-white rounded-md shadow-[0_4px_20px_rgba(0,0,0,0.15)] border border-slate-200 overflow-hidden z-50">
                            <div
                                class="bg-slate-50 border-b border-slate-200 px-3 py-2 text-[12px] font-bold text-slate-600">
                                Gợi ý câu hỏi nhanh
                            </div>
                            <div class="flex flex-col max-h-48 overflow-y-auto custom-scrollbar">
                                <button type="button"
                                    @click="useSuggestion('Thầy/Cô cho em hỏi về việc đăng ký tín chỉ học kỳ tới ạ?')"
                                    class="text-left px-3 py-2.5 text-[13px] text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-100 last:border-0 truncate">Đăng
                                    ký tín chỉ</button>
                                <button type="button"
                                    @click="useSuggestion('Dạ Thầy/Cô ơi, em muốn xem lại điểm rèn luyện kỳ trước thì xem ở đâu ạ?')"
                                    class="text-left px-3 py-2.5 text-[13px] text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-100 last:border-0 truncate">Hỏi
                                    về điểm rèn luyện</button>
                                <button type="button"
                                    @click="useSuggestion('Thầy/Cô cho em hỏi cách nộp minh chứng hoạt động ngoại khóa ạ?')"
                                    class="text-left px-3 py-2.5 text-[13px] text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-100 last:border-0 truncate">Nộp
                                    minh chứng ngoại khóa</button>
                                <button type="button"
                                    @click="useSuggestion('Dạ em chào Thầy/Cô, em thấy mình bị cảnh báo học vụ, em cần làm gì tiếp theo ạ?')"
                                    class="text-left px-3 py-2.5 text-[13px] text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-100 last:border-0 truncate">Hỏi
                                    về Cảnh báo học vụ</button>
                                <button type="button"
                                    @click="useSuggestion('Thầy/Cô cho em hỏi điều kiện để được xét học bổng học kỳ này là gì ạ?')"
                                    class="text-left px-3 py-2.5 text-[13px] text-slate-700 hover:bg-blue-50 hover:text-blue-600 transition-colors border-b border-slate-100 last:border-0 truncate">Điều
                                    kiện xét học bổng</button>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Nút Đóng Khung Chat --}}
                <button @click="isOpen = false"
                    class="w-8 h-8 rounded-full hover:bg-white/20 flex items-center justify-center transition-colors shrink-0">
                    <span class="material-symbols-outlined !text-[22px]">close</span>
                </button>
            </div>
        </div>

        {{-- CẢNH 1: DANH BẠ VÀ TÌM KIẾM --}}
        <div x-show="!activeChat" x-transition class="flex-1 overflow-y-auto bg-white custom-scrollbar flex flex-col">
            <div class="p-2 border-b border-slate-100 sticky top-0 bg-white/95 backdrop-blur-sm z-10 shrink-0">
                <div class="relative flex items-center w-full h-9 rounded-sm bg-slate-100 px-3">
                    <span class="material-symbols-outlined !text-[16px] text-slate-400 mr-2">search</span>
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

                <button x-show="showScrollButton" @click="scrollToBottom(true)"
                    class="absolute bottom-4 left-1/2 -translate-x-1/2 z-30 w-8 h-8 rounded-full border border-blue-500 text-blue-500 bg-white/90 backdrop-blur-sm shadow-sm flex items-center justify-center hover:bg-blue-50 transition-colors">
                    <span class="material-symbols-outlined !text-[20px]">expand_more</span>
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

                            <div x-show="msg.sender_id != currentUserId"
                                class="w-6 h-6 shrink-0 mr-1.5 flex items-end">
                                <div x-show="msg.isLastInGroup"
                                    class="w-6 h-6 rounded-full bg-slate-200 text-slate-600 flex items-center justify-center text-[10px] font-bold border border-white shadow-sm"
                                    x-text="activeChat?.name.charAt(0)"></div>
                            </div>

                            <button x-show="msg.sender_id != currentUserId && activeMenuId !== msg.id"
                                @click="activeMenuId = msg.id"
                                class="text-slate-300 hover:text-slate-500 opacity-0 group-hover:opacity-100 p-1 order-last ml-1"
                                title="Tùy chọn">
                                <span class="material-symbols-outlined !text-[18px]">more_vert</span>
                            </button>

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

                        <div x-show="activeMenuId === msg.id" x-transition.opacity x-cloak
                            class="flex items-center gap-1.5 mt-1 w-full"
                            :class="msg.sender_id == currentUserId ? 'justify-end pr-1' : 'justify-start pl-8'">
                            <button @click="executeDeleteForMe(msg.id)"
                                class="text-[10px] px-2 py-0.5 bg-slate-100 text-slate-700 border border-slate-200 rounded-sm hover:bg-slate-200 font-medium transition-colors">
                                Xóa phía tôi
                            </button>
                            <button x-show="canRecall(msg)" @click="executeRecall(msg.id)"
                                class="text-[10px] px-2 py-0.5 bg-red-50 text-red-600 border border-red-200 rounded-sm hover:bg-red-500 hover:text-white font-medium transition-colors">
                                Thu hồi
                            </button>
                            <button @click="activeMenuId = null"
                                class="text-[10px] px-2 py-0.5 text-slate-400 hover:underline">Đóng</button>
                        </div>

                        <div x-show="msg.isLastInGroup && msg.sender_id != currentUserId && activeMenuId !== msg.id"
                            class="mt-0.5 mb-2 pl-9 flex items-center justify-start">
                            <span class="text-[9px] text-slate-400" x-text="formatTimeOnly(msg.created_at)"></span>
                        </div>

                        <div x-show="msg.isLastInGroup && msg.sender_id == currentUserId && activeMenuId !== msg.id"
                            class="mt-0.5 mb-2 pr-1 flex items-center justify-end gap-1">
                            <span class="text-[9px] text-slate-400" x-text="formatTimeOnly(msg.created_at)"></span>
                            <template x-if="msg.isLastOfMe">
                                <div class="flex items-center">
                                    <template x-if="msg.read_at">
                                        <span class="text-[10px] text-blue-500 flex items-center gap-0.5 ml-1">
                                            <span class="material-symbols-outlined !text-[12px]">done_all</span> Đã xem
                                        </span>
                                    </template>
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

            <div class="p-2 bg-white border-t border-slate-200 shrink-0">
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
                    <button type="button" @click="$refs.fileInput.click()"
                        class="w-10 h-10 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-sm transition-all shrink-0">
                        <span class="material-symbols-outlined !text-[22px]">attach_file</span>
                    </button>
                    <div class="flex-1">
                        <textarea x-ref="chatInput" x-model="newMessage" @keydown.enter.prevent="if(!event.shiftKey) sendMessage()"
                            :disabled="selectedFile" :placeholder="selectedFile ? 'Đã chọn tệp...' : 'Aa...'"
                            :class="selectedFile ? 'bg-slate-200 cursor-not-allowed opacity-70 border-slate-300' :
                                'bg-slate-50 border-slate-200'"
                            class="w-full border rounded-sm pl-3 pr-3 py-[9px] text-[13px] text-slate-700 focus:ring-0 focus:border-blue-400 focus:bg-white resize-none overflow-hidden max-h-[100px] min-h-[40px] custom-scrollbar block transition-all shadow-inner leading-normal"
                            rows="1" @input="resizeTextarea($event.target)"></textarea>
                    </div>
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
    .custom-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .custom-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    function chatWidget() {
        return {
            isOpen: false,
            activeChat: null,
            contacts: [],
            searchQuery: '',
            messages: [],
            newMessage: '',
            selectedFile: null,
            currentConversationId: null,
            isLoadingContacts: true,
            isLoadingMessages: false,
            activeMenuId: null,
            pollInterval: null,
            isUserScrollingUp: false,
            showScrollButton: false,
            currentUserId: {{ auth()->id() }},
            showSuggestions: false,

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

                // Thêm Event Listener lắng nghe lệnh từ Modal Đặt lịch
                window.addEventListener('fill-chat-appointment', (e) => {
                    let detail = e.detail;
                    this.isOpen = true; // Mở khung chat lên

                    // Hàm xử lý mở chat và điền tin
                    const applyMessage = () => {
                        let contact = this.contacts.find(c => c.id == detail.contactId);
                        if (contact) {
                            this.openChat(contact);
                            this.newMessage = detail.message; // Điền đoạn văn bản vào ô input

                            // Đợi render xong rồi focus và chỉnh chiều cao textarea
                            setTimeout(() => {
                                if (this.$refs.chatInput) {
                                    this.resizeTextarea(this.$refs.chatInput);
                                    this.$refs.chatInput.focus();
                                }
                            }, 300);
                        }
                    };

                    // Nếu danh bạ chưa load, load xong rồi mới điền
                    if (this.contacts.length === 0) {
                        this.isLoadingContacts = true;
                        fetch('/chat/contacts')
                            .then(res => res.json())
                            .then(data => {
                                this.contacts = data;
                                this.isLoadingContacts = false;
                                applyMessage();
                            });
                    } else {
                        applyMessage();
                    }
                });
            },

            get filteredContacts() {
                if (this.searchQuery.trim() === '') return this.contacts;
                let query = this.removeAccents(this.searchQuery.toLowerCase());
                return this.contacts.filter(c => {
                    let normalizedName = this.removeAccents(c.name.toLowerCase());
                    return normalizedName.includes(query);
                });
            },

            removeAccents(str) {
                return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').replace(/đ/g, 'd').replace(/Đ/g, 'D');
            },

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

                    msg.showDateDivider = !prevDate || (msgDate - prevDate) > 600000;

                    let isSameSenderAsPrev = prevMsg && prevMsg.sender_id == msg.sender_id;
                    let isCloseToPrev = prevDate && (msgDate - prevDate) < 60000 && !msg.showDateDivider;
                    msg.isFirstInGroup = !(isSameSenderAsPrev && isCloseToPrev);

                    let isSameSenderAsNext = nextMsg && nextMsg.sender_id == msg.sender_id;
                    let isCloseToNext = nextMsg && (new Date(nextMsg.created_at) - msgDate) < 60000;
                    let nextMsgHasDivider = nextMsg && (new Date(nextMsg.created_at) - msgDate) > 600000;
                    msg.isLastInGroup = !(isSameSenderAsNext && isCloseToNext && !nextMsgHasDivider);

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
                this.searchQuery = '';
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

            canRecall(msg) {
                if (msg.sender_id != this.currentUserId) return false;
                if (msg.is_recalled) return false;
                const diffMins = (new Date() - new Date(msg.created_at)) / 60000;
                return diffMins <= 60;
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
                            alert(data.message);
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

            useSuggestion(text) {
                this.newMessage = text;
                this.showSuggestions = false;
                setTimeout(() => {
                    this.$refs.chatInput.focus();
                    this.resizeTextarea(this.$refs.chatInput);
                }, 50);
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
                yesterfully.setDate(now.getDate() - 1);
                if (date.toDateString() === yesterday.toDateString()) {
                    return 'Hôm qua ' + timeStr;
                }

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
