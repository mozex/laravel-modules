<?php

namespace Mozex\Modules\Scouts;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class CommandsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Commands;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->extending(Command::class)
            ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
            ->sortBy(Sort::Name);
    }
}
