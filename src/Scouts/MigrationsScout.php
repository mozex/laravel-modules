<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ModuleDirectoryScout;
use Mozex\Modules\Enums\AssetType;

class MigrationsScout extends ModuleDirectoryScout
{
    public function asset(): AssetType
    {
        return AssetType::Migrations;
    }
}
