<?php

namespace Mozex\Modules;

use Illuminate\Support\Collection;
use Mozex\Modules\Concerns\FindsSeeder;

class Modules
{
    use FindsSeeder;

    /**
     * @return Collection<array-key, array{module: string, path: string, order: int}>
     */
    public function getModulesAssets(array $patterns): Collection
    {
        $assets = collect();

        foreach ($patterns as $pattern) {
            if (is_array($pattern)) {
                $flags = $pattern['flags'] ?? 0;
                $pattern = $pattern['pattern'];
            }

            $assets->push(
                ...glob(
                    pattern: base_path($pattern),
                    flags: $flags ?? 0
                )
            );
        }

        return $assets
            ->map(function (string $path) {
                preg_match(
                    '/Modules\/(.*?)\//',
                    $path,
                    $result
                );

                return [
                    'module' => mb_strtolower($result[1]),
                    'path' => $path,
                    'order' => (int) (config('modules.modules', [])[$result[1]]['order'] ?? 9999),
                ];
            })
            ->filter($this->isModuleActive(...))
            ->sortBy('order');
    }

    public function isModuleActive(array $asset): bool
    {
        $module = config('modules.modules', [])[$asset['module']] ?? null;

        if (empty($module) || ! is_array($module) || ! isset($module['active'])) {
            return true;
        }

        return $module['active'];
    }

    public function makeNamespaceForAsset(array $asset): string
    {
        return str($asset['path'])
            ->after(realpath(base_path()).DIRECTORY_SEPARATOR)
            ->replace(['/', '.php'], ['\\', ''])
            ->explode('\\')
            ->map(ucfirst(...))
            ->implode('\\');
    }
}
