<?php

namespace Mozex\Modules\Concerns;

use Laravel\Nova\Actions\ActionResource;
use Laravel\Nova\Nova;
use Laravel\Nova\Resource;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait SupportsNova
{
    public function bootNova(): void
    {
        if (class_exists(Nova::class)) {
            Nova::serving(function (): void {
                Nova::resources(
                    Modules::getModulesAssets(config('modules.nova_resource_patterns'))
                        ->map(Modules::makeNamespaceForAsset(...))
                        ->filter($this->isNovaResource(...))
                        ->sort()
                        ->all()
                );
            });
        }
    }

    /**
     * @throws ReflectionException
     */
    public function isNovaResource(string $namespace): bool
    {
        return is_subclass_of($namespace, Resource::class)
            && ! (new ReflectionClass($namespace))->isAbstract()
            && ! is_subclass_of($namespace, ActionResource::class);
    }
}
