<?php

declare(strict_types=1);

namespace Mozex\Modules\Features\SupportFilament;

use Mozex\Modules\Enums\AssetType;

class FilamentPagesScout extends FilamentScout
{
    public function asset(): AssetType
    {
        return AssetType::FilamentPages;
    }
}
