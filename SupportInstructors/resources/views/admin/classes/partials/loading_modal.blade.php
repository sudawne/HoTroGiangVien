<div id="loadingModal" class="fixed inset-0 z-[200] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-sm border border-slate-200 dark:border-slate-700">
                <div class="px-4 pb-4 pt-5 sm:p-6 text-center">
                    {{-- Spinner --}}
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 dark:bg-indigo-900/20 mb-4">
                        <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-lg font-bold leading-6 text-slate-900 dark:text-white mb-2"
                        id="loading-modal-title">
                        Đang xử lý...
                    </h3>

                    {{-- Thanh tiến trình --}}
                    <div id="progress-container" class="hidden mt-4 w-full">
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2 overflow-hidden dark:bg-slate-700">
                            <div id="progress-bar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-300"
                                style="width: 0%"></div>
                        </div>
                        <p id="progress-text" class="text-xs font-medium text-blue-600 dark:text-blue-400">Đang gửi
                            0/0...</p>
                    </div>

                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-2" id="loading-modal-desc">
                        Vui lòng không tắt trình duyệt!
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
