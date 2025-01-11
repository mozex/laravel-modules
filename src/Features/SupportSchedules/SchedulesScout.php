<?php

namespace Mozex\Modules\Features\SupportSchedules;

use Mozex\Modules\Contracts\ConsoleKernel;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Mozex\Modules\Facades\Modules;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

class SchedulesScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Schedules;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(ConsoleKernel::class))
            ->custom(
                fn (DiscoveredClass $structure): bool => $structure->name === 'Kernel'
                    && str_ends_with(
                        $structure->namespace,
                        Modules::moduleNameFromNamespace($structure->namespace).'\\Console'
                    )
            );
    }
}
