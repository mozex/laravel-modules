<?php

namespace Mozex\Modules\Contracts;

use Spatie\StructureDiscoverer\Discover;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException;

abstract class ModuleClassScout extends BaseScout
{
    public function getWithoutCache(): array
    {
        try {
            $result = $this->definition()->getWithoutCache();
        } catch (DirectoryNotFoundException) {
            return [];
        }

        return $this->transform($result);
    }

    abstract protected function definition(): Discover;
}