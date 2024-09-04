<?php

namespace App\Providers;

use App\Events\MessageSent;
use App\Listeners\MessageListener;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        Event::listen(
            MessageSent::class,
            MessageListener::class,
        );
    }
}
