<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tự động share biến $routePrefix cho TẤT CẢ CÁC VIEW (*)
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                // Nếu đã đăng nhập: Check role
                $routePrefix = \Illuminate\Support\Facades\Auth::user()->role_id == 1 ? 'admin.' : 'lecturer.';
                $view->with('routePrefix', $routePrefix);
            } else {
                // Nếu chưa đăng nhập (đang ở trang login) thì gán mặc định để không bị lỗi Undefined
                $view->with('routePrefix', 'admin.');
            }
        });
    }
}
