<?php

namespace Mozex\Modules\Contracts;

use Spatie\StructureDiscoverer\Discover;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

abstract class ModuleClassScout extends BaseScout
{
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

    abstract protected function definition(): Discover;
}
