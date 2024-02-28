<?php

namespace Mozex\Modules\Scouts;

use Livewire\Component;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
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
            ->extending(Component::class);
    }
}
