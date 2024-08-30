<?php

namespace Mozex\Modules\Features\SupportConfigs;

use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Support\Facades\File;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class ConfigsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Configs->isDeactive()) {
            return;
        }

        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            AssetType::Configs->scout()->collect()
                ->each(function (array $asset) use ($config): void {
                    $key = File::name($asset['path']);

                    $config->set(
                        key: $key,
                        value: AssetType::Configs->config()['priority']
                            ? array_merge(
                                $config->get($key, []),
                                require $asset['path']
                            )
                            : array_merge(
                                require $asset['path'],
                                $config->get($key, [])
                            )
                    );
                });
        }
    }
}
