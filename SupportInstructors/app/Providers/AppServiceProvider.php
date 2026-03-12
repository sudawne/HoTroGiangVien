<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $routePrefix = \Illuminate\Support\Facades\Auth::user()->role_id == 1 ? 'admin.' : 'lecturer.';
                $view->with('routePrefix', $routePrefix);
            } else {
                $view->with('routePrefix', 'admin.');
            }
        });
    }
}
