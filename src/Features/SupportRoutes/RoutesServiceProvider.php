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

class RoutesServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Routes->isDeactive()) {
            return;
        }

        $config = AssetType::Routes->config();
        /** @var array<array-key, string> $commandsFilenames */
        $commandsFilenames = $config['commands_filenames'];
        /** @var array<array-key, string> $channelsFilenames */
        $channelsFilenames = $config['channels_filenames'];

        [$commands, $rest] = AssetType::Routes->scout()->collect()
            ->partition(
                fn (array $asset) => in_array(File::name($asset['path']), $commandsFilenames)
            );

        [$channels, $routes] = $rest
            ->partition(
                fn (array $asset) => in_array(File::name($asset['path']), $channelsFilenames)
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
