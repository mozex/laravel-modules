<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Console\Scheduling\Schedule;

abstract class ConsoleKernel
{
    abstract public function schedule(Schedule $schedule): void;
}
