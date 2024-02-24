<?php

namespace Mozex\Modules\Scouts;

use Illuminate\Database\Seeder;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Data\DiscoveredStructure;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class SeedersScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Seeders;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->custom(
                fn (DiscoveredStructure $structure) => $structure->name == Modules::moduleNameFromNamespace($structure->namespace).'DatabaseSeeder'
            )
            ->extending(Seeder::class)
            ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
            ->sortBy(Sort::Name);
    }
}
