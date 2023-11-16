<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Console\Scheduling\Schedule;

abstract class ConsoleKernel
{
    public function schedule(Schedule $schedule): void
    {
        //
    }
}
