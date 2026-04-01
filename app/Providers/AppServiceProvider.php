<?php

namespace App\Providers;

use App\Models\Lead;
use App\Observers\LeadObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
      Lead::observe(LeadObserver::class);
    }
}
