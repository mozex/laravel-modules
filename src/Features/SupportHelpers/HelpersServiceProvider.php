<?php

namespace Mozex\Modules\Features\SupportHelpers;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class HelpersServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Helpers;
    }

    #[Override]
    public function register(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                require_once $asset['path'];
            });
    }
}
