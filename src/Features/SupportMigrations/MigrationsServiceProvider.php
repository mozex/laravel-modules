<?php

namespace Mozex\Modules\Features\SupportMigrations;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class MigrationsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Migrations->isDeactive()) {
            return;
        }

        $this->loadMigrationsFrom(
            AssetType::Migrations->scout()->collect()
                ->pluck('path')
                ->toArray()
        );
    }
}
