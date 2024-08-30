<?php

namespace Mozex\Modules\Features\SupportRoutes;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;

class RoutesServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Routes->isDeactive()) {
            return;
        }

        [$commands, $rest] = AssetType::Routes->scout()->collect()
            ->partition(
                fn (array $asset) => collect(AssetType::Routes->config()['commands_filenames'])
                    ->contains(File::name($asset['path']))
            );

        [$channels, $routes] = $rest
            ->partition(
                fn (array $asset) => collect(AssetType::Routes->config()['channels_filenames'])
                    ->contains(File::name($asset['path']))
            );

        $this->callAfterResolving(Kernel::class, function (Kernel $kernel) use ($commands) {
            // Compatibility with Laravel 10
            if (method_exists($kernel, 'addCommandRoutePaths')) {
                $kernel->addCommandRoutePaths(
                    $commands->pluck('path')->all()
                );
            }
        });

        $this->app->booted(function () use ($channels) {
            if ($channels->isNotEmpty()) {
                Broadcast::routes();
            }

            $channels->each(function (array $asset): void {
                require $asset['path'];
            });
        });

        if ($this->app->routesAreCached()) {
            return;
        }

        $routes->each(function (array $asset): void {
            Route::group(
                attributes: Modules::getRouteGroup(
                    name: File::name($asset['path'])
                ),
                routes: $asset['path']
            );
        });
    }
}
