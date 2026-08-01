<?php

namespace Mozex\Modules\Features\SupportCommands;

use Illuminate\Console\Application as Artisan;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class CommandsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Commands;
    }

    #[Override]
    public function boot(): void
    {
        // Deferred so web requests never pay for the scout read.
        Artisan::starting(function (Artisan $artisan): void {
            $artisan->resolveCommands(
                static::asset()->scout()->collect()
                    ->pluck('namespace')
                    ->all()
            );
        });
    }
}
