<?php

namespace Mozex\Modules\Features\SupportServiceProviders;

use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Contracts\ModuleClassScout;
use Mozex\Modules\Enums\AssetType;
use Spatie\StructureDiscoverer\Discover;

class ServiceProvidersScout extends ModuleClassScout
{
    public function asset(): AssetType
    {
        return AssetType::ServiceProviders;
    }

    protected function definition(): Discover
    {
        return parent::definition()
            ->extending(ServiceProvider::class);
    }
}
