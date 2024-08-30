<?php

namespace Mozex\Modules\Features\SupportNova;

use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Resource;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
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
            ->extending(Resource::class)
            ->custom(
                fn (DiscoveredClass $structure) => ! in_array(ActionResource::class, $structure->extendsChain)
            );
    }
}
