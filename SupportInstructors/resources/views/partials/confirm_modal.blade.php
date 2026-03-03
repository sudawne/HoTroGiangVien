{{-- GLOBAL CONFIRMATION MODAL --}}
<div id="global-confirm-modal" class="fixed inset-0 z-[100] hidden" role="dialog" aria-modal="true">
    {{-- Backdrop (Màn mờ) --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="window.closeConfirm()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                
                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        {{-- Icon (Sẽ thay đổi màu bằng JS) --}}
                        <div id="gcm-icon-bg" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10 transition-colors">
                            <span id="gcm-icon" class="material-symbols-outlined text-blue-600 dark:text-blue-400">help</span>
                        </div>
                        
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            {{-- Tiêu đề --}}
                            <h3 id="gcm-title" class="text-base font-semibold leading-6 text-slate-900 dark:text-white">
                                Xác nhận
                            </h3>
                            {{-- Nội dung --}}
                            <div class="mt-2">
                                <p id="gcm-message" class="text-sm text-slate-500 dark:text-slate-400">
                                    Bạn có chắc chắn muốn thực hiện hành động này?
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Nút bấm --}}
                <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-100 dark:border-slate-700">
                    <button type="button" id="gcm-btn-confirm"
                        class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-colors">
                        Đồng ý
                    </button>
                    <button type="button" onclick="window.closeConfirm()" 
                        class="mt-3 inline-flex w-full justify-center rounded-md bg-white dark:bg-slate-700 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-slate-200 shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 sm:mt-0 sm:w-auto transition-colors">
                        Hủy bỏ
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let confirmCallback = null;
    window.showConfirm = function(title, message, callback, type = 'primary') {
        // 1. Cập nhật nội dung
        document.getElementById('gcm-title').textContent = title;
        document.getElementById('gcm-message').innerHTML = message; // Dùng innerHTML để hỗ trợ thẻ <br>, <strong>
        confirmCallback = callback;

        // 2. Cập nhật Giao diện (Màu sắc nút bấm & Icon)
        const btnConfirm = document.getElementById('gcm-btn-confirm');
        const iconBg = document.getElementById('gcm-icon-bg');
        const icon = document.getElementById('gcm-icon');

        if (type === 'danger') {
            // Style cho hành động nguy hiểm (Xóa) -> Màu Đỏ
            btnConfirm.className = "inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors";
            btnConfirm.textContent = 'Xóa ngay';
            iconBg.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10";
            icon.className = "material-symbols-outlined text-red-600 dark:text-red-400";
            icon.textContent = "warning";
        } else {
            // Style mặc định -> Màu Xanh
            btnConfirm.className = "inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 sm:ml-3 sm:w-auto transition-colors";
            btnConfirm.textContent = 'Đồng ý';
            iconBg.className = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/30 sm:mx-0 sm:h-10 sm:w-10";
            icon.className = "material-symbols-outlined text-blue-600 dark:text-blue-400";
            icon.textContent = "help";
        }

        // 3. Hiển thị Modal
        document.getElementById('global-confirm-modal').classList.remove('hidden');
    };

    window.closeConfirm = function() {
        document.getElementById('global-confirm-modal').classList.add('hidden');
        confirmCallback = null; // Reset callback để tránh lỗi
    };

    // Xử lý sự kiện bấm nút Đồng ý
    document.getElementById('gcm-btn-confirm').addEventListener('click', function() {
        if (confirmCallback) {
            confirmCallback(); // Chạy hàm callback được truyền vào
        }
        window.closeConfirm();
    });
</script>