<?php

namespace Mozex\Modules;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\DiscoverConditions\DiscoverCondition;

class ExtendsDiscoverCondition extends DiscoverCondition
{
    public function __construct(public string $extends) {}

    public function satisfies(DiscoveredStructure $discoveredData): bool
    {
        /** @var DiscoveredClass $discoveredData */

        return $discoveredData->extends == $this->extends
            || is_subclass_of($discoveredData->extends, $this->extends);
    }
}
