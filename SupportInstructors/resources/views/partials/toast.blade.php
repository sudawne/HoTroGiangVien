<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none">
</div>

<script>
    window.showToast = function(type, message) {
        const container = document.getElementById('toast-container');   
        const config = {
            success: {
                icon: 'check_circle',
                bg: 'bg-white dark:bg-slate-800',
                border: 'border-l-4 border-green-500',
                text: 'text-green-600',
                title: 'Thành công'
            },
            error: {
                icon: 'error',
                bg: 'bg-white dark:bg-slate-800',
                border: 'border-l-4 border-red-500',
                text: 'text-red-600',
                title: 'Lỗi'
            },
            warning: {
                icon: 'warning',
                bg: 'bg-white dark:bg-slate-800',
                border: 'border-l-4 border-orange-500',
                text: 'text-orange-500',
                title: 'Cảnh báo'
            },
            info: {
                icon: 'info',
                bg: 'bg-white dark:bg-slate-800',
                border: 'border-l-4 border-blue-500',
                text: 'text-blue-500',
                title: 'Thông tin'
            }
        };

        const style = config[type] || config.info;

        // Tạo phần tử HTML
        const toast = document.createElement('div');
        toast.className = `${style.bg} ${style.border} shadow-lg rounded-r-md pointer-events-auto flex items-start gap-3 p-4 min-w-[300px] max-w-md transform translate-x-full transition-all duration-300 ease-out`;
        
        toast.innerHTML = `
            <span class="material-symbols-outlined !text-[24px] ${style.text} mt-0.5">${style.icon}</span>
            <div class="flex-1">
                <h4 class="font-bold text-sm text-slate-900 dark:text-white">${style.title}</h4>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 leading-tight">${message}</p>
            </div>
            <button onclick="this.parentElement.remove()" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                <span class="material-symbols-outlined !text-[18px]">close</span>
            </button>
        `;

        // Thêm vào container
        container.appendChild(toast);

        // Hiệu ứng trượt ra (Sau 100ms để trình duyệt kịp render)
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 100);

        // Tự động xóa sau 4 giây
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentElement) toast.remove();
            }, 300); // Đợi hiệu ứng biến mất xong mới xóa DOM
        }, 4000);
    }
</script>

{{-- Tự động kích hoạt nếu có Flash Message từ Controller Laravel --}}
@if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('success', "{{ session('success') }}");
        });
    </script>
@endif

@if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('error', "{{ session('error') }}");
        });
    </script>
@endif

@if(session('warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('warning', "{{ session('warning') }}");
        });
    </script>
@endif

@if ($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($errors->all() as $error)
                showToast('error', "{{ $error }}");
            @endforeach
        });
    </script>
@endif