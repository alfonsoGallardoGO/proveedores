<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

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
        Inertia::share([
            'auth.user' => function () {
                if (auth()->check()) {
                    return [
                        'id' => auth()->id(),
                        'name' => auth()->user()->name,
                        'roles' => auth()->user()->getRoleNames(),
                        'permissions' => auth()->user()->getPermissionNames(),
                    ];
                }

                return null;
            },
        ]);

    }
}
