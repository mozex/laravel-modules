<?php

declare(strict_types=1);

namespace Modules\Second\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class Kernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        $schedule->exec('second-scheduled-command-1')
            ->daily();

        $schedule->exec('second-scheduled-command-2')
            ->daily();
    }
}
