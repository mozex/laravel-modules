<?php

namespace Mozex\Modules\Scouts;

use Mozex\Modules\Contracts\ConsoleKernel;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class SchedulesScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Schedules;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->extending(ConsoleKernel::class)
            ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
            ->sortBy(Sort::Name);
    }
}