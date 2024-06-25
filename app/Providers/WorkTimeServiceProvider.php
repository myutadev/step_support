<?php

namespace App\Providers;

use App\Services\WorkTimeService;
use Illuminate\Support\ServiceProvider;

class WorkTimeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(WorkTimeService::class, function ($app) {
            return new WorkTimeService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
