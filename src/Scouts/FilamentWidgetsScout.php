<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\FilamentScout;
use Mozex\Modules\Enums\AssetType;

class FilamentWidgetsScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentWidgets;
    }
}
