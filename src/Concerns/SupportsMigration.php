<?php

namespace Mozex\Modules\Concerns;

use Mozex\Modules\Facades\Modules;

trait SupportsMigration
{
    public function bootMigrations(): void
    {
        $this->loadMigrationsFrom(
            Modules::getModulesAssets(config('modules.migration_patterns'))
                ->pluck('path')
                ->toArray()
        );
    }
}
