<?php

namespace Mozex\Modules\Features\SupportFilament;

use Mozex\Modules\Enums\AssetType;

class FilamentResourcesScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentResources;
    }
}
