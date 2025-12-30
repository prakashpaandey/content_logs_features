<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;

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
        // Share clients with the sidebar component globally
        View::composer('components.sidebar', function ($view) {
            if (Auth::check()) {
                $view->with('clients', Auth::user()->clients()->orderBy('name')->get());
            } else {
                $view->with('clients', collect());
            }
        });
    }
}
