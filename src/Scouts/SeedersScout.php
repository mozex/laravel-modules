<?php

namespace Mozex\Modules\Scouts;

use Illuminate\Database\Seeder;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

class SeedersScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Seeders;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->extending(Seeder::class)
            ->custom(
                fn (DiscoveredClass $structure) => $structure->name == Modules::moduleNameFromNamespace($structure->namespace).'DatabaseSeeder'
            );
    }
}
