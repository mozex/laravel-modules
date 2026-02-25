<?php

namespace Mozex\Modules\Tests;

use Orchestra\Testbench\Foundation\Console\Kernel as ConsoleKernel;
use Override;
use Throwable;

class Kernel extends ConsoleKernel
{
    protected $commands = [];

    /**
     * @throws Throwable
     */
    #[Override]
    protected function reportException(Throwable $e)
    {
        throw $e;
    }

    #[Override]
    protected function shouldDiscoverCommands(): bool
    {
        return static::class === self::class;
    }
}
