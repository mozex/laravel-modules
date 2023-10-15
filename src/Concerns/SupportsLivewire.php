<?php

namespace Mozex\Modules\Concerns;

use Livewire\Component;
use Livewire\Livewire;
use Mozex\Modules\Facades\Modules;
use ReflectionClass;
use ReflectionException;

trait SupportsLivewire
{
    public function bootLivewire(): void
    {
        Modules::getModulesAssets(config('modules.livewire_component_patterns'))
            ->map(Modules::makeNamespaceForAsset(...))
            ->filter($this->isLivewireComponent(...))
            ->each(function (string $namespace): void {
                Livewire::component(
                    str(class_basename($namespace))->kebab()->toString(),
                    $namespace
                );
            });
    }

    /**
     * @throws ReflectionException
     */
    public function isLivewireComponent(string $namespace): bool
    {
        return is_subclass_of($namespace, Component::class)
            && ! (new ReflectionClass($namespace))->isAbstract();
    }
}
