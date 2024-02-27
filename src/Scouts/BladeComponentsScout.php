<?php

namespace Mozex\Modules\Scouts;

use Illuminate\View\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class BladeComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::BladeComponents;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->extending(Component::class)
            ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
            ->full()
            ->sortBy(Sort::Name);
    }
}
