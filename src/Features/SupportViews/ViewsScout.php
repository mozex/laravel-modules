<?php

namespace Mozex\Modules\Features\SupportViews;

use Mozex\Modules\Contracts\ModuleDirectoryScout;
use Mozex\Modules\Enums\AssetType;

class ViewsScout extends ModuleDirectoryScout
{
    public function asset(): AssetType
    {
        return AssetType::Views;
    }
}
