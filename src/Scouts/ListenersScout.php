<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ModuleDirectoryScout;
use Mozex\Modules\Enums\AssetType;

class ListenersScout extends ModuleDirectoryScout
{
    public function asset(): AssetType
    {
        return AssetType::Listeners;
    }
}
