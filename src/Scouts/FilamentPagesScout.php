<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\FilamentScout;
use Mozex\Modules\Enums\AssetType;

class FilamentPagesScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentPages;
    }
}
