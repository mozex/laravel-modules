<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Console\Scheduling\Schedule;
use Mozex\Modules\Contracts\ConsoleKernel;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait SupportSchedules
{
    public function bootSchedules(): void
    {
        $this->app->booted(function () {
            Modules::getModulesAssets(config('modules.kernel_patterns'))
                ->map(Modules::makeNamespaceForAsset(...))
                ->filter($this->isKernel(...))
                ->each(function (string $namespace) {
                    /** @var ConsoleKernel $kernel */
                    $kernel = $this->app->make($namespace);

                    $kernel->schedule(
                        $this->app
                            ->make(Schedule::class)
                            ->useCache(
                                $kernel->scheduleCache()
                            )
                    );
                });
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
