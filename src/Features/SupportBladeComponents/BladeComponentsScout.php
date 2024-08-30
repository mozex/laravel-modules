<?php

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\View\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Discover;

class BladeComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->extending(Component::class);
    }
}
