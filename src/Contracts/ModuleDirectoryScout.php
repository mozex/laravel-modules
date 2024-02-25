<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Support\Facades\File;

abstract class ModuleDirectoryScout extends BaseScout
{
    public function getWithoutCache(): array
    {
        $assets = collect();

        collect($this->patterns())
            ->each(fn (string $pattern) => $assets->push(
                ...File::glob(
                    pattern: $pattern,
                    flags: GLOB_ONLYDIR
                )
            ));

        return $this->transform(
            $assets->toArray()
        );
    }
}
