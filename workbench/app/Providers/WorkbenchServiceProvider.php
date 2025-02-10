<?php

namespace App\Providers;

use Closure;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Facades\Modules;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Modules::setBasePath(dirname(__DIR__, 2));

        Modules::routeGroup(
            name: 'custom',
            prefix: 'custom',
            as: 'custom::',
            middleware: ['web', 'api'],
        );

        Modules::routeGroup(
            name: 'localized',
            prefix: 'localized',
            as: 'localized::',
            middleware: ['web', 'api'],
        );

        Modules::registerRoutesUsing(
            name: 'localized',
            closure: function (array $attributes, array|Closure|string $routes) {
                Route::group([
                    'prefix' => 'en',
                ], function () use ($attributes, $routes) {
                    Route::group(
                        attributes: $attributes,
                        routes: $routes
                    );
                });
            }
        );
    }

    public function boot(): void {}
}
