<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait GetsSchedules
{
    public function schedules(Schedule $schedule): void
    {
        Modules::getModulesAssets(config('modules.kernel_patterns'))
            ->map(Modules::makeNamespaceForAsset(...))
            ->filter($this->isKernel(...))
            ->each(function (string $namespace) use ($schedule) {
                (new $namespace)->schedule($schedule);
            });
    }

    /**
     * @throws ReflectionException
     */
    public function isKernel(string $namespace): bool
    {
        return is_subclass_of($namespace, ConsoleKernel::class)
            && ! (new ReflectionClass($namespace))->isAbstract();
    }
}
