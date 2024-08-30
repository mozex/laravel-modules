<?php

namespace Mozex\Modules\Features\SupportSchedules;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class SchedulesServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Schedules->isDeactive()) {
            return;
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            AssetType::Schedules->scout()->collect()
                ->each(function (array $asset) use ($schedule): void {
                    (new $asset['namespace'])->schedule($schedule);
                });
        });
    }
}
