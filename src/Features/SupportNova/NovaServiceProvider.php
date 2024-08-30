<?php

namespace Mozex\Modules\Features\SupportNova;

use Laravel\Nova\Nova;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class NovaServiceProvider extends Feature
{
    public function boot(): void
    {
        if (! class_exists(Nova::class)) {
            return;
        }

        if (AssetType::NovaResources->isDeactive()) {
            return;
        }

        Nova::serving(function (): void {
            Nova::resources(
                AssetType::NovaResources->scout()->collect()
                    ->pluck('namespace')
                    ->toArray()
            );
        });
    }
}
