<?php

namespace App\Providers;

use App\Models\Lead;
use App\Observers\LeadObserver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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

        View::composer('_global.Admin.Header', function ($view) {
            $user = Auth::user();

            
            if (!$user) {
                $view->with([
                    'headerNotifications' => collect(),
                    'headerUnreadNotificationsCount' => 0,
                ]);

                return;
            }

            $view->with([
                'headerNotifications' => $user->notifications()->latest()->limit(5)->get(),
                'headerUnreadNotificationsCount' => $user->unreadNotifications()->count(),
            ]);
        });
    }
}
