<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ModuleFileScout;
use Mozex\Modules\Enums\AssetType;

class RoutesScout extends ModuleFileScout
{
    public function asset(): AssetType
    {
        return AssetType::Routes;
    }
}
