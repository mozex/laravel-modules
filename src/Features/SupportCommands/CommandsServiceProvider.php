<?php

namespace Mozex\Modules\Features\SupportCommands;

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
        $this->commands(
            static::asset()->scout()->collect()
                ->pluck('namespace')
                ->toArray()
        );
    }
}
