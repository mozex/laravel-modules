<?php

namespace Mozex\Modules\Scouts;

use Livewire\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class LivewireComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::LivewireComponents;
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
