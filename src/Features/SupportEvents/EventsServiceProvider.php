<?php

namespace Mozex\Modules\Features\SupportEvents;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Mozex\Modules\Enums\AssetType;

class EventsServiceProvider extends EventServiceProvider
{
    public static function asset(): AssetType
    {
        return AssetType::Listeners;
    }

    public static function shouldRegisterFeature(): bool
    {
        return static::asset()->isActive();
    }

    public function shouldDiscoverEvents(): bool
    {
        return static::shouldRegisterFeature();
    }

    /**
     * @return array<string>
     */
    protected function discoverEventsWithin(): array
    {
        return static::asset()->scout()
            ->collect()
            ->pluck('path')
            ->toArray();
    }

    protected function configureEmailVerification(): void {}
}
