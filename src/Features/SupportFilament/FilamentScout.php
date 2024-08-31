<?php

namespace Mozex\Modules\Features\SupportFilament;

use Exception;
use Mozex\Modules\Contracts\ModuleDirectoryScout;
use Spatie\Regex\Regex;

abstract class FilamentScout extends ModuleDirectoryScout
{
    /**
     * @param  array<array-key, string>  $result
     * @return array<array-key, array{module: string, path: string, namespace: class-string, panel: string}>
     */
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
