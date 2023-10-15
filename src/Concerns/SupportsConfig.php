<?php

namespace Mozex\Modules\Concerns;

use Mozex\Modules\Facades\Modules;

trait SupportsConfig
{
    public function bootConfigs(): void
    {
        Modules::getModulesAssets(config('modules.config_patterns'))
            ->each(function (array $asset): void {
                $fileName = basename($asset['path'], '.php');
                $targetFileName = $fileName === 'config' ? $asset['module'] : $fileName;

                $this->publishes([
                    $asset['path'] => config_path("{$targetFileName}.php"),
                ], 'config');

                $this->mergeConfigWithProiorityFrom(
                    $asset['path'],
                    $fileName === 'config' ? $asset['module'] : $fileName
                );
            });
    }
}
