<?php

namespace Mozex\Modules\Contracts;

abstract class ModuleDirectoryScout extends BaseScout
{
    public function getWithoutCache(): array
    {
        $assets = collect();

        collect($this->patterns())
            ->each(fn (string $pattern) => $assets->push(
                ...glob(
                    pattern: $pattern,
                    flags: GLOB_ONLYDIR
                )
            ));

        return $this->transform(
            $assets->toArray()
        );
    }
}
