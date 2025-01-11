<?php

namespace Mozex\Modules\Features\SupportNova;

use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Resource;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\ExtendsDiscoverCondition;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

class NovaResourcesScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::NovaResources;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->custom(new ExtendsDiscoverCondition(Resource::class)) // @phpstan-ignore-line
            ->custom(
                fn (DiscoveredClass $structure): bool => ! in_array(ActionResource::class, $structure->extendsChain) // @phpstan-ignore-line
            );
    }
}
