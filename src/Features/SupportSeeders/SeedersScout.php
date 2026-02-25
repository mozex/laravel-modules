<?php

namespace Mozex\Modules\Features\SupportSeeders;

use Illuminate\Database\Seeder;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Mozex\Modules\Facades\Modules;
use Override;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

class SeedersScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::Seeders;
    }

    #[Override]
    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Seeder::class))
            ->custom(
                fn (DiscoveredClass $structure): bool => $structure->name === Modules::moduleNameFromNamespace($structure->namespace).'DatabaseSeeder'
            );
    }
}
