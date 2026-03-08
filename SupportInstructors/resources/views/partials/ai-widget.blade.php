@if (auth()->check() && (auth()->user()->hasRole('LECTURER') || auth()->user()->hasRole('ADMIN')))
    <div x-data="aiWidget()" class="fixed bottom-6 left-6 z-[100] font-sans">

        {{-- Nút Mở AI --}}
        <button @click="isAIOpen = !isAIOpen"
            class="w-12 h-12 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-full shadow-[0_4px_14px_0_rgba(147,51,234,0.39)] hover:scale-105 transition-all duration-200 flex items-center justify-center relative">
            <span class="material-symbols-outlined !text-[24px]">smart_toy</span>
            <span
                class="absolute -top-1 -right-1 w-3.5 h-3.5 bg-yellow-400 border-2 border-white rounded-full animate-pulse"></span>
        </button>

        {{-- Khung Chat AI --}}
        <div x-show="isAIOpen" x-transition.opacity.duration.300ms x-cloak
            class="absolute bottom-16 left-0 w-[350px] h-[500px] bg-white rounded-sm shadow-[0_5px_25px_-5px_rgba(0,0,0,0.2)] border border-slate-200 flex flex-col overflow-hidden">

            {{-- Header AI --}}
            <div
                class="bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-4 py-3 flex justify-between items-center z-10 shrink-0 shadow-sm">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined !text-[24px]">auto_awesome</span>
                    <div class="flex flex-col">
                        <h3 class="font-bold text-[14px] leading-tight">Trợ lý Cố vấn AI</h3>
                        <p class="text-[11px] text-purple-200 font-medium">Sẵn sàng phân tích dữ liệu</p>
                    </div>
                </div>
                <button @click="isAIOpen = false" class="text-white hover:text-purple-200"><span
                        class="material-symbols-outlined !text-[20px]">close</span></button>
            </div>

            {{-- Khu vực tin nhắn --}}
            <div class="flex-1 overflow-y-auto p-4 space-y-4 custom-scrollbar bg-slate-50 flex flex-col"
                id="ai-messages-container">
                <div class="flex items-start gap-2 w-full">
                    <div
                        class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white shrink-0 shadow-sm mt-1">
                        <span class="material-symbols-outlined !text-[16px]">smart_toy</span>
                    </div>
                    <div
                        class="p-3 bg-white text-slate-700 border border-slate-200 rounded-2xl rounded-tl-sm text-[13px] shadow-sm leading-relaxed max-w-[85%]">
                        Chào Thầy/Cô <b>{{ auth()->user()->name }}</b>, tôi đã đồng bộ dữ liệu lớp sinh hoạt. Thầy/Cô
                        cần tìm hiểu về sinh viên nào, hoặc cần soạn thảo thông báo gì không ạ?
                    </div>
                </div>

                <template x-for="msg in aiMessages" :key="msg.id">
                    <div class="w-full flex flex-col" :class="msg.role === 'user' ? 'items-end' : 'items-start'">
                        <div class="flex gap-2 w-full"
                            :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">

                            {{-- Avatar AI --}}
                            <div x-show="msg.role === 'ai'"
                                class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white shrink-0 shadow-sm mt-1">
                                <span class="material-symbols-outlined !text-[16px]">smart_toy</span>
                            </div>

                            <div :class="msg.role === 'user' ? 'bg-indigo-600 text-white rounded-2xl rounded-tr-sm' :
                                'bg-white text-slate-700 border border-slate-200 rounded-2xl rounded-tl-sm'"
                                class="px-3 py-2 text-[13px] shadow-sm flex flex-col break-words leading-relaxed max-w-[85%]">
                                {{-- Dùng x-html để AI có thể in đậm/xuống dòng (markdown cơ bản) --}}
                                <span x-html="formatAIMessage(msg.content)"></span>
                            </div>
                        </div>
                    </div>
                </template>

                {{-- Hiệu ứng AI đang gõ --}}
                <div x-show="isAILoading" class="flex items-start gap-2 w-full">
                    <div
                        class="w-7 h-7 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white shrink-0 mt-1">
                        <span class="material-symbols-outlined !text-[16px] animate-spin">sync</span>
                    </div>
                    <div
                        class="p-2 bg-white border border-slate-200 rounded-2xl rounded-tl-sm shadow-sm flex items-center gap-1">
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce"></div>
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.1s">
                        </div>
                        <div class="w-1.5 h-1.5 bg-slate-400 rounded-full animate-bounce" style="animation-delay: 0.2s">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form nhập --}}
            <div class="p-2 bg-white border-t border-slate-200 shrink-0">
                <form @submit.prevent="sendToAI()" class="flex items-center gap-2 relative">
                    <div class="flex-1">
                        <textarea x-ref="aiInput" x-model="aiPrompt" @keydown.enter.prevent="if(!event.shiftKey) sendToAI()"
                            class="w-full bg-slate-50 border border-slate-200 rounded-sm pl-3 pr-3 py-[9px] text-[13px] text-slate-700 placeholder:text-slate-400 focus:ring-0 focus:border-purple-400 focus:bg-white resize-none overflow-hidden max-h-[100px] min-h-[40px] custom-scrollbar block transition-all shadow-inner"
                            rows="1" placeholder="Hỏi AI về sinh viên..."></textarea>
                    </div>
                    <button type="submit" :disabled="isAILoading"
                        class="w-10 h-10 bg-indigo-600 text-white rounded-sm flex items-center justify-center shadow-sm transition-all shrink-0"
                        :class="(!aiPrompt.trim() || isAILoading) ? 'opacity-40 cursor-not-allowed grayscale' :
                        'hover:bg-indigo-700 active:scale-95'">
                        <span class="material-symbols-outlined !text-[20px] ml-0.5">send</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function aiWidget() {
            return {
                isAIOpen: false,
                aiPrompt: '',
                aiMessages: [],
                isAILoading: false,

                sendToAI() {
                    if (!this.aiPrompt.trim() || this.isAILoading) return;

                    let userText = this.aiPrompt.trim();

                    // Thêm tin nhắn của user vào UI
                    this.aiMessages.push({
                        id: Date.now(),
                        role: 'user',
                        content: userText
                    });
                    this.aiPrompt = '';
                    this.$refs.aiInput.style.height = 'auto';
                    this.isAILoading = true;
                    this.scrollAIBottom();

                    fetch('/ai/ask', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                message: userText
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            this.isAILoading = false;
                            if (data.reply) {
                                this.aiMessages.push({
                                    id: Date.now(),
                                    role: 'ai',
                                    content: data.reply
                                });
                            } else {
                                this.aiMessages.push({
                                    id: Date.now(),
                                    role: 'ai',
                                    content: 'Lỗi: ' + data.error
                                });
                            }
                            this.scrollAIBottom();
                        })
                        .catch(err => {
                            this.isAILoading = false;
                            this.aiMessages.push({
                                id: Date.now(),
                                role: 'ai',
                                content: 'Có lỗi xảy ra khi kết nối.'
                            });
                            this.scrollAIBottom();
                        });
                },

                formatAIMessage(text) {
                    // Xử lý markdown cơ bản của Gemini: in đậm **text**, xuống dòng \n
                    return text.replace(/\*\*(.*?)\*\*/g, '<b>$1</b>').replace(/\n/g, '<br>');
                },

                scrollAIBottom() {
                    setTimeout(() => {
                        let container = document.getElementById('ai-messages-container');
                        if (container) container.scrollTop = container.scrollHeight;
                    }, 50);
                }
            }
        }
    </script>
@endif
