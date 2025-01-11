<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Livewire\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Spatie\StructureDiscoverer\Discover;

class LivewireComponentsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::LivewireComponents;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Component::class));
    }
}
