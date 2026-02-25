<?php

namespace Mozex\Modules\Features\SupportRoutes;

use Closure;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;

class RoutesServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Routes;
    }

    #[Override]
    public function boot(): void
    {
        $config = static::asset()->config();

        [$commands, $rest] = static::asset()->scout()->collect()
            ->partition(
                fn (array $asset) => in_array(
                    File::name($asset['path']),
                    $config['commands_filenames']
                )
            );

        [$channels, $routes] = $rest
            ->partition(
                fn (array $asset) => in_array(
                    File::name($asset['path']),
                    $config['channels_filenames']
                )
            );

        $this->callAfterResolving(Kernel::class, function (Kernel $kernel) use ($commands): void {
            // Compatibility with Laravel 10
            /** @phpstan-ignore-next-line */
            if (method_exists($kernel, 'addCommandRoutePaths')) {
                $kernel->addCommandRoutePaths(
                    $commands->pluck('path')->all()
                );
            }
        });

        $this->app->booted(function () use ($channels): void {
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
            $name = File::name($asset['path']);

            $this->getRegisterRoutesUsing($name)(
                $this->getRouteAttributes($name),
                $asset['path']
            );
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function getRouteAttributes(string $name): array
    {
        if (! isset(Modules::getRouteGroups()[$name])) {
            return [];
        }

        return collect(Modules::getRouteGroups()[$name])
            ->filter()
            ->map(fn (mixed $value) => is_callable($value) ? $value() : $value)
            ->toArray();
    }

    public function getRegisterRoutesUsing(string $name): Closure
    {
        return Modules::getRegisterRoutesUsing()[$name] ?? function (array $attributes, array|Closure|string $routes) {
            Route::group(
                attributes: $attributes,
                routes: $routes
            );
        };
    }
}
