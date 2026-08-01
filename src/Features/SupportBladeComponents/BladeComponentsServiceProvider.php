<?php

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\Support\Facades\Blade;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class BladeComponentsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    #[Override]
    public function boot(): void
    {
        BladeComponentsScout::instance()->collect()
            ->each(function (array $asset): void {
                Blade::component(
                    class: $asset['namespace'],
                    // Fallback for cache files built by older package versions.
                    alias: $asset['alias'] ?? $this->getViewName($asset, static::asset())
                );
            });
    }
}
