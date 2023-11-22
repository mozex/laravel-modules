<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait SupportsServiceProviders
{
    public function registerServicePorviders(): void
    {
        Modules::getModulesAssets(config('modules.service_provider_patterns'))
            ->map(Modules::makeNamespaceForAsset(...))
            ->filter($this->isServiceProvider(...))
            ->each(function (string $namespace) {
                $this->app->register($namespace);
            });
    }

    /**
     * @throws ReflectionException
     */
    public function isServiceProvider(string $namespace): bool
    {
        return is_subclass_of($namespace, ServiceProvider::class)
            && ! (new ReflectionClass($namespace))->isAbstract();
    }
}
