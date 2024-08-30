<?php

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\Support\Facades\Blade;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class BladeComponentsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::BladeComponents->isDeactive()) {
            return;
        }

        AssetType::BladeComponents->scout()->collect()
            ->each(function (array $asset): void {
                Blade::component(
                    class: $asset['namespace'],
                    alias: $this->getViewName($asset, AssetType::BladeComponents)
                );
            });
    }
}
