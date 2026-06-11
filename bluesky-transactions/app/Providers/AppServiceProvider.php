<?php

namespace App\Providers;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Share active countries with every view — single source of truth.
        // Uses a static variable so the DB/cache is hit at most once per request,
        // even when multiple nested views are composed.
        View::composer('*', function ($view) {
            static $countries = null;
            if ($countries === null) {
                $countries = Cache::remember('active_countries', 3600, fn () =>
                    Country::where('is_active', true)->orderBy('name')->get()
                );
            }
            $view->with('activeCountries', $countries);
        });

        // Invalidate the cache whenever a country record is saved or deleted.
        Country::saved(fn ()   => Cache::forget('active_countries'));
        Country::deleted(fn () => Cache::forget('active_countries'));
    }
}
