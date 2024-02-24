<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ModuleFileScout;
use Mozex\Modules\Enums\AssetType;

class ConfigsScout extends ModuleFileScout
{
    public function asset(): AssetType
    {
        return AssetType::Configs;
    }
}
