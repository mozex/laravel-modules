<?php

namespace Mozex\Modules\Features\SupportCommands;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Spatie\StructureDiscoverer\Discover;

class CommandsScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Commands;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Command::class));
    }
}
