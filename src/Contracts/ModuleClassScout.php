<?php

namespace Mozex\Modules\Contracts;

use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;
use Spatie\StructureDiscoverer\Enums\Sort;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

abstract class ModuleClassScout extends BaseScout
{
    /**
     * @return array<array-key, array{module: string, path: string, namespace: class-string}>
     */
    public function getWithoutCache(): array
    {
        try {
            return $this->transform(
                $this->definition()->getWithoutCache()
            );
        } catch (DirectoryNotFoundException) {
            return [];
        }
    }

    protected function definition(): Discover
    {
        return Discover::in(...$this->patterns())
            ->parallel()
            ->classes()
            ->full()
            ->custom(fn (DiscoveredClass $structure): bool => ! $structure->isAbstract)
            ->sortBy(Sort::Name);
    }
}
