<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/profile';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {

        //TODO: Fix the 404 on prod when looking for livewire.js
// https://github.com/livewire/livewire/discussions/6328
//        Livewire::setUpdateRoute(function ($handle) {
//            return Route::post(env("APP_SUBPATH") . '/livewire/update', $handle);
//        });
//
////        https://laracasts.com/discuss/channels/livewire/has-anyone-got-livewire-3-running-in-production-on-a-nginx-server?page=1&replyId=916472
//        Livewire::setScriptRoute(function ($handle) {
//            return Route::get(env("APP_SUBPATH") . '/livewire/livewirejs', $handle);
//        });

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}
