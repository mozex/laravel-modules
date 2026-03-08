<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Illuminate\Support\Str;
use Livewire\Finder\Finder;
use Override;

/**
 * Workaround for https://github.com/livewire/livewire/pull/10076
 *
 * Livewire's Finder::generateNameFromClass() does not prepend the namespace
 * key when stripping a classNamespace prefix, causing Route::livewire() with
 * class references to fail for components registered via addNamespace().
 *
 * Remove this class once the upstream fix is merged.
 */
class NamespacedFinder extends Finder
{
    public function __construct(Finder $original)
    {
        $this->classLocations = $original->classLocations;
        $this->viewLocations = $original->viewLocations;
        $this->classNamespaces = $original->classNamespaces;
        $this->viewNamespaces = $original->viewNamespaces;
        $this->classComponents = $original->classComponents;
        $this->viewComponents = $original->viewComponents;
    }

    /**
     * @param  string  $class
     */
    #[Override]
    protected function generateNameFromClass($class): string // @pest-ignore-type @phpstan-ignore typeCoverage.paramTypeCoverage (parent has no type hint)
    {
        /** @var string $class */
        $class = str_replace(
            ['/', '\\'],
            '.',
            $this->normalizePath($class)
        );

        $fullName = implode('.', array_map(
            Str::kebab(...),
            explode('.', $class),
        ));

        if (str_starts_with($fullName, '.')) {
            $fullName = substr($fullName, 1);
        }

        if (str_ends_with($fullName, '.index')) {
            $fullName = substr($fullName, 0, -6);
        }

        /** @var array<string, array{classNamespace: string}> $registeredNamespaces */
        $registeredNamespaces = $this->classNamespaces;

        /** @var array<int|string, string> $classNamespaces */
        $classNamespaces = array_map(
            fn (array $ns): string => $ns['classNamespace'],
            $registeredNamespaces,
        ) + array_values($this->classLocations);

        foreach ($classNamespaces as $key => $classNamespace) {
            $namespace = implode('.', array_map(
                Str::kebab(...),
                explode('.', str_replace(
                    ['/', '\\'],
                    '.',
                    $this->normalizePath($classNamespace),
                )),
            ));

            if (str_starts_with($fullName, $namespace)) {
                $name = substr($fullName, strlen($namespace) + 1);

                return is_string($key) ? $key.'::'.$name : $name;
            }
        }

        return $fullName;
    }
}
