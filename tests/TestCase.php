<?php

namespace Mozex\Modules\Tests;

use Illuminate\Contracts\Console\Kernel;
use Mozex\Modules\Tests\Kernel as NewKernel;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function resolveApplicationConsoleKernel($app): void
    {
        $app->singleton(
            Kernel::class,
            NewKernel::class
        );
    }
}
