<?php

namespace Mozex\Modules\Features\SupportHelpers;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class HelpersServiceProvider extends Feature
{
    public function register(): void
    {
        if (AssetType::Helpers->isDeactive()) {
            return;
        }

        AssetType::Helpers->scout()->collect()
            ->each(function (array $asset): void {
                require_once $asset['path'];
            });
    }
}
