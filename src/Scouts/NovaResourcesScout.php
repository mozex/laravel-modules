<?php

namespace Mozex\Modules\Scouts;

use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Resource;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class NovaResourcesScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::NovaResources;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->extending(Resource::class)
            ->custom(
                fn (DiscoveredClass $structure) => ! $structure->isAbstract
                    && ! in_array(ActionResource::class, $structure->extendsChain)
            )
            ->full()
            ->sortBy(Sort::Name);
    }
}
