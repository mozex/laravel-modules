<?php

namespace Mozex\Modules\Concerns;

use Mozex\Modules\Facades\Modules;

trait SupportsHelpers
{
    public function registerHelpers(): void
    {
        Modules::getModulesAssets(config('modules.helper_patterns'))
            ->each(function (array $asset): void {
                require_once $asset['path'];
            });
    }
}
