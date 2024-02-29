<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\FilamentScout;
use Mozex\Modules\Enums\AssetType;

class FilamentResourcesScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentResources;
    }
}
