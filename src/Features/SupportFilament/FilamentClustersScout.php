<?php

namespace Mozex\Modules\Features\SupportFilament;

use Mozex\Modules\Contracts\FilamentScout;
use Mozex\Modules\Enums\AssetType;

class FilamentClustersScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentClusters;
    }
}
