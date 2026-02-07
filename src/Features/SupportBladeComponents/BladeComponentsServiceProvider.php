<?php

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\Support\Facades\Blade;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class BladeComponentsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    public function boot(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                Blade::component(
                    class: $asset['namespace'],
                    alias: $this->getViewName($asset, static::asset())
                );
            });
    }
}
