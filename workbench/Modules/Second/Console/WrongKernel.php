<?php

declare(strict_types=1);

namespace Modules\Second\Console;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;

class WrongKernel extends ConsoleKernel
{
    public function schedule(Schedule $schedule): void {}
}
