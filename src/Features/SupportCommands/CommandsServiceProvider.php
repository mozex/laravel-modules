<?php

namespace Mozex\Modules\Features\SupportCommands;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class CommandsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Commands->isDeactive()) {
            return;
        }

        $this->commands(
            AssetType::Commands->scout()->collect()
                ->pluck('namespace')
                ->toArray()
        );
    }
}
