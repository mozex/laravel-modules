<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ModuleFileScout;
use Mozex\Modules\Enums\AssetType;

class HelpersScout extends ModuleFileScout
{
    public function asset(): AssetType
    {
        return AssetType::Helpers;
    }
}
