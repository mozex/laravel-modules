<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Mozex\Modules\Contracts\ModuleDirectoryScout;
use Mozex\Modules\Enums\AssetType;

class LivewireComponentsScout extends ModuleDirectoryScout
{
    public function asset(): AssetType
    {
        return AssetType::LivewireComponents;
    }
}
