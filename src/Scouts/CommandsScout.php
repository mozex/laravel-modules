<?php

namespace Mozex\Modules\Scouts;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
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
            ->extending(Command::class);
    }
}
