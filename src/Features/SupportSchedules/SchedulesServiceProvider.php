<?php

namespace Mozex\Modules\Features\SupportSchedules;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class SchedulesServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Schedules;
    }

    public function boot(): void
    {
        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            static::asset()->scout()->collect()
                ->each(function (array $asset) use ($schedule): void {
                    (new $asset['namespace'])->schedule($schedule);
                });
        });
    }
}
