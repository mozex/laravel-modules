<?php

declare(strict_types=1);

namespace Modules\First\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->exec('first-scheduled-command-1')
            ->daily();
    }
}
