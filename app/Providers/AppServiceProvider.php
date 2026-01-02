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

        // Nepali Translation Helper (Romanized BS Labels)
        View::share('nepaliTranslate', function($text, $type = 'month') {
            $months = [
                'January' => 'Magh', 'February' => 'Falgun', 'March' => 'Chaitra',
                'April' => 'Baisakh', 'May' => 'Jestha', 'June' => 'Asar',
                'July' => 'Shrawan', 'August' => 'Bhadra', 'September' => 'Ashwin',
                'October' => 'Kartik', 'November' => 'Mangsir', 'December' => 'Poush'
            ];

            $numMonths = [
                1 => 'Baisakh', 2 => 'Jestha', 3 => 'Asar', 4 => 'Shrawan',
                5 => 'Bhadra', 6 => 'Ashwin', 7 => 'Kartik', 8 => 'Mangsir',
                9 => 'Poush', 10 => 'Magh', 11 => 'Falgun', 12 => 'Chaitra'
            ];
            
            if ($type === 'month') {
                return $months[$text] ?? ($numMonths[$text] ?? $text);
            }

            if ($type === 'number') {
                return $text; 
            }

            if ($type === 'year') {
                // If text is a full date string or just year
                return $text; // Usually passed as BS year now
            }

            return $text;
        });

        // BS to AD and AD to BS Helpers
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
    }
}
