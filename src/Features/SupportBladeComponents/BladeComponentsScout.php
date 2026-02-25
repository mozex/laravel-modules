<?php

namespace Mozex\Modules\Features\SupportBladeComponents;

use Illuminate\View\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Override;
use Spatie\StructureDiscoverer\Discover;

class BladeComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    #[Override]
    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Component::class));
    }
}
