<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Support\Facades\Config;
use Mozex\Modules\Facades\Modules;

trait SupportsView
{
    public function bootViews(): void
    {
        Modules::getModulesAssets(config('modules.view_patterns'))
            ->each(function (array $asset): void {
                $this->publishes([
                    $asset['path'] => resource_path('views/vendor/'.$asset['module']),
                ], ['views', "{$asset['module']}-module-views"]);

                /** @var array<string> $viewPaths */
                $viewPaths = Config::get('view.paths');

                $this->loadViewsFrom(
                    collect($viewPaths)
                        ->filter(fn (string $path) => is_dir("{$path}/vendor/{$asset['module']}"))
                        ->map(fn (string $path) => "{$path}/vendor/{$asset['module']}")
                        ->push($asset['path'])
                        ->toArray(),
                    $asset['module']
                );
            });
    }
}
