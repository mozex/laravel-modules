<?php

namespace Mozex\Modules\Features\SupportMigrations;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class MigrationsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Migrations;
    }

    #[Override]
    public function boot(): void
    {
        $this->loadMigrationsFrom(
            static::asset()->scout()->collect()
                ->pluck('path')
                ->toArray()
        );
    }
}
