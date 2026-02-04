<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Client;
use Illuminate\Support\Facades\Gate;
use App\Models\Permission;
use Illuminate\Support\Facades\Schema;

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
            if (Auth::check() && Gate::allows('clients.view')) {
                $view->with('clients', Client::orderBy('name')->get());
            } else {
                $view->with('clients', collect());
            }
        });

        // Nepali Translation Helper (Romanized BS Labels)
        View::share('nepaliTranslate', function($text, $type = 'month') {
            

            $numMonths = [
                1 => 'Baisakh', 2 => 'Jestha', 3 => 'Asar', 4 => 'Shrawan',
                5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
                9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
            ];
            
            if ($type === 'month') {
                return $numMonths[$text] ?? $text;
            }

            if ($type === 'number') {
                return $text; 
            }

            if ($type === 'year') {
                return $text; 
            }

            return $text;
        });

        
        View::share('dateHelpers', new class {
            public function adToBs($adDate) {
                return \App\Helpers\NepaliDateHelper::adToBs($adDate);
            }

            public function representativeAdToBs($adDate) {
                return \App\Helpers\NepaliDateHelper::representativeAdToBs($adDate);
            }

            public function bsToAd($bsMonth, $bsYear) {
                return \App\Helpers\NepaliDateHelper::bsToAd($bsMonth, $bsYear);
            }
        });

        // Dynamic Permissions Mapping
        if (Schema::hasTable('permissions')) {
            try {
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    Gate::define($permission->slug, function ($user) use ($permission) {
                        return $user->hasPermission($permission->slug);
                    });
                }
            } catch (\Exception $e) {
                
            }
        }

        // Grant full access to Admin
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });
    }
}
