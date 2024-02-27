<?php

namespace Mozex\Modules;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Mozex\Modules\Enums\AssetType;

class ModulesEventServiceProvider extends EventServiceProvider
{
    public function shouldDiscoverEvents(): bool
    {
        return AssetType::Listeners->isActive();
    }

    protected function discoverEventsWithin(): array
    {
        return AssetType::Listeners->scout()
            ->collect()
            ->pluck('path')
            ->toArray();
    }
}
