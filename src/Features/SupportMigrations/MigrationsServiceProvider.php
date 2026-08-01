<?php

namespace Mozex\Modules\Features\SupportMigrations;

use Illuminate\Database\Migrations\Migrator;
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
        // Deferred so web requests never pay for the scout read.
        $this->callAfterResolving(
            'migrator',
            function (Migrator $migrator): void {
                static::asset()->scout()->collect()
                    ->each(fn (array $asset) => $migrator->path($asset['path']));
            }
        );
    }
}
