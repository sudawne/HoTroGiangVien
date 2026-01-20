{{-- MODAL CONFIRM MAIL --}}
<div id="confirmMailModal" class="fixed inset-0 z-[110] hidden" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div
                class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e1e2d] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-700">
                <div class="p-6">
                    <div class="flex items-center gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="material-symbols-outlined text-blue-600 !text-[24px]">mark_email_unread</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Gửi thông tin tài khoản?</h3>
                            <p class="text-sm text-slate-500 mt-1">
                                Bạn có muốn hệ thống tự động gửi email (MSSV & Mật khẩu) cho danh sách sinh viên vừa
                                import không?
                            </p>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" id="btn-no-mail"
                            class="px-4 py-2 bg-slate-100 text-slate-700 font-medium rounded-sm hover:bg-slate-200 text-sm">
                            Không gửi (Chỉ tạo)
                        </button>
                        <button type="button" id="btn-yes-mail"
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-sm hover:bg-blue-700 shadow-sm text-sm flex items-center gap-2">
                            <span class="material-symbols-outlined !text-[16px]">send</span> Đồng ý gửi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
