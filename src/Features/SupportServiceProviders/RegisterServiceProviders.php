<?php

namespace Mozex\Modules\Features\SupportServiceProviders;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class RegisterServiceProviders extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::ServiceProviders;
    }

    #[Override]
    public function register(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                $this->app->register($asset['namespace']);
            });
    }
}
