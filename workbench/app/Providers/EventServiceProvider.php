<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Mozex\Modules\Facades\Modules;
use Override;

class EventServiceProvider extends ServiceProvider
{
    #[Override]
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    #[Override]
    protected function discoverEventsWithin(): array
    {
        return [
            realpath(Modules::basePath('app/Listeners')),
        ];
    }

    #[Override]
    protected function eventDiscoveryBasePath(): string
    {
        return realpath(Modules::basePath());
    }
}
