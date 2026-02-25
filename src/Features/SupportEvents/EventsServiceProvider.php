<?php

namespace Mozex\Modules\Features\SupportEvents;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Mozex\Modules\Enums\AssetType;
use Override;

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

    #[Override]
    public function shouldDiscoverEvents(): bool
    {
        return static::shouldRegisterFeature();
    }

    /**
     * @return array<string>
     */
    #[Override]
    protected function discoverEventsWithin(): array
    {
        return static::asset()->scout()
            ->collect()
            ->pluck('path')
            ->toArray();
    }

    #[Override]
    protected function configureEmailVerification(): void {}
}
