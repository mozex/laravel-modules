<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Env;

abstract class ConsoleKernel
{
    protected Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function schedule(Schedule $schedule): void
    {
        //
    }

    public function scheduleCache(): ?string
    {
        return $this->app['config']->get(
            'cache.schedule_store',
            Env::get('SCHEDULE_CACHE_DRIVER')
        );
    }
}
