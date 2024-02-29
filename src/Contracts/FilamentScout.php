<?php

namespace Mozex\Modules\Contracts;

use Exception;
use Spatie\Regex\Regex;

abstract class FilamentScout extends ModuleDirectoryScout
{
    public function transform(array $result): array
    {
        return collect(parent::transform($result))
            ->map(function (array $item) {
                $panel = null;

                foreach ($this->asset()->patterns() as $pattern) {
                    $panel ??= Regex::match(
                        pattern: str($pattern)
                            ->replace('*', '(.*?)')
                            ->replace('/', '\/')
                            ->prepend('/')
                            ->append('/')
                            ->toString(),
                        subject: str(realpath($item['path']))
                            ->replace('\\', '/')
                    )->groupOr(2, '');
                }

                if (empty($panel)) {
                    throw new Exception("Panel not found for {$item['path']}");
                }

                return [
                    ...$item,
                    'panel' => strtolower($panel),
                ];
            })
            ->toArray();
    }
}
