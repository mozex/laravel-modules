<?php

namespace Mozex\Modules\Contracts;

abstract class ModuleFileScout extends BaseScout
{
    public function getWithoutCache(): array
    {
        $assets = collect();

        collect($this->patterns())
            ->each(fn (string $pattern) => $assets->push(
                ...glob(
                    pattern: $pattern,
                )
            ));

        return $this->transform(
            $assets->toArray()
        );
    }
}
