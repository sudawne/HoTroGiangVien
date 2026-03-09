@props(['title', 'description' => '', 'breadcrumbs' => [], 'routePrefix' => 'admin.'])

<div>
    <nav aria-label="Breadcrumb" class="flex text-sm text-slate-500 dark:text-slate-400 mb-1">
        <ol class="flex items-center space-x-2">
            <li>
                <a class="hover:text-primary transition-colors" href="{{ route($routePrefix . 'dashboard') }}">Trang
                    chủ</a>
            </li>

            @foreach ($breadcrumbs as $breadcrumb)
                <li>
                    <span class="material-symbols-outlined !text-[12px]">chevron_right</span>
                </li>

                @if (!$loop->last && isset($breadcrumb['url']))
                    <li>
                        <a class="hover:text-primary transition-colors" href="{{ $breadcrumb['url'] }}">
                            {{ $breadcrumb['label'] }}
                        </a>
                    </li>
                @else
                    <li>
                        <span class="font-medium text-slate-900 dark:text-slate-200">
                            {{ $breadcrumb['label'] }}
                        </span>
                    </li>
                @endif
            @endforeach
        </ol>
    </nav>

    <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $title }}</h1>

    @if ($description)
        <p class="text-slate-500 dark:text-slate-400 text-xs mt-0.5">{{ $description }}</p>
    @endif
</div>
