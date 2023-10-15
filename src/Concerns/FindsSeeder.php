<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Database\Seeder;
use ReflectionClass;
use ReflectionException;

trait FindsSeeder
{
    public function getSeeders(): array
    {
        return $this->getModulesAssets(config('modules.seeder_patterns'))
            ->map($this->makeNamespaceForAsset(...))
            ->filter($this->isSeeder(...))
            ->toArray();
    }

    /**
     * @throws ReflectionException
     */
    public function isSeeder(string $namespace): bool
    {
        return is_subclass_of($namespace, Seeder::class)
            && ! (new ReflectionClass($namespace))->isAbstract();
    }
}
