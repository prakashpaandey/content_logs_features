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

            // Direct Number to BS Month mapping
            $numMonths = [
                1 => 'Magh', 2 => 'Falgun', 3 => 'Chaitra', 4 => 'Baisakh',
                5 => 'Jestha', 6 => 'Asar', 7 => 'Shrawan', 8 => 'Bhadra',
                9 => 'Ashwin', 10 => 'Kartik', 11 => 'Mangsir', 12 => 'Poush'
            ];
            
            if ($type === 'month') {
                return $months[$text] ?? ($numMonths[$text] ?? $text);
            }

            if ($type === 'number') {
                return $text; // Standard numerals for Romanized look
            }

            if ($type === 'year') {
                return (string)((int)$text + 56); // Standard numerals BS Year
            }

            return $text;
        });
    }
}
