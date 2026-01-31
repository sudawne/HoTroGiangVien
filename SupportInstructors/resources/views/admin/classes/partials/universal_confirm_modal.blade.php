<div id="universalModal" class="fixed inset-0 z-[150] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div id="uni-modal-icon-bg"
                            class="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center bg-blue-100">
                            <span id="uni-modal-icon"
                                class="material-symbols-outlined text-[24px] text-blue-600">help</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white" id="uni-modal-title">
                                Xác nhận hành động
                            </h3>
                            <p class="text-sm text-slate-500 mt-1" id="uni-modal-desc">
                                Bạn có chắc chắn muốn thực hiện hành động này không?
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="btn-uni-cancel"
                            class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-sm hover:bg-slate-200 text-sm">
                            Hủy bỏ
                        </button>
                        <button type="button" id="btn-uni-confirm"
                            class="px-4 py-2 text-white font-medium rounded-sm shadow-sm text-sm flex items-center gap-2 bg-blue-600 hover:bg-blue-700">
                            <span class="material-symbols-outlined !text-[16px]">check</span>
                            <span id="uni-modal-btn-text">Xác nhận</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
