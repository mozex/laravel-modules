<?php

namespace Mozex\Modules\Features\SupportConfigs;

use Mozex\Modules\Contracts\ModuleFileScout;
use Mozex\Modules\Enums\AssetType;

class ConfigsScout extends ModuleFileScout
{
    public function asset(): AssetType
    {
        return AssetType::Configs;
    }
}
