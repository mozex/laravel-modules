<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Mozex\Modules\Facades\Modules;

class EventServiceProvider extends ServiceProvider
{
    public function shouldDiscoverEvents(): bool
    {
        return true;
    }

    protected function discoverEventsWithin(): array
    {
        return [
            realpath(Modules::basePath('app/Listeners')),
        ];
    }

    protected function eventDiscoveryBasePath(): string
    {
        return realpath(Modules::basePath());
    }
}
