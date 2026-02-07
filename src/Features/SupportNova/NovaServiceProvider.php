<?php

namespace Mozex\Modules\Features\SupportNova;

use Laravel\Nova\Nova;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class NovaServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::NovaResources;
    }

    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Nova::class);
    }

    public function boot(): void
    {
        /** @phpstan-ignore class.notFound */
        Nova::serving(function (): void {
            // @phpstan-ignore-next-line
            Nova::resources(
                static::asset()->scout()->collect()
                    ->pluck('namespace')
                    ->toArray()
            );
        });
    }
}
