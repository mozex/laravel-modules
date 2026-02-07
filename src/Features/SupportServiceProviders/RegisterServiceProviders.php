<?php

namespace Mozex\Modules\Features\SupportServiceProviders;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class RegisterServiceProviders extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::ServiceProviders;
    }

    public function register(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                $this->app->register($asset['namespace']);
            });
    }
}
