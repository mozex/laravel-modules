<?php

declare(strict_types=1);

namespace Mozex\Modules\Tests;

use Illuminate\Contracts\Console\Kernel;
use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Tests\Kernel as NewKernel;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function setUp(): void
    {
        // Scout singletons and the in-memory cache layer are process-wide
        // statics; without a reset, a test that mutates discovery config can
        // poison the cached results seen by later tests.
        BaseScout::clearInstances();
        StaticDiscoverCacheDriver::clear();

        parent::setUp();
    }

    protected function resolveApplicationConsoleKernel($app): void
    {
        $app->singleton(
            Kernel::class,
            NewKernel::class
        );
    }
}
