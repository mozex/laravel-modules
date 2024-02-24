<?php

namespace Mozex\Modules\Scouts;

use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;

class ServiceProvidersScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::ServiceProviders;
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->extending(ServiceProvider::class)
            ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
            ->sortBy(Sort::Name);
    }
}
