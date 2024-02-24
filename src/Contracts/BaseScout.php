<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Support\Collection;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;

abstract class BaseScout
{
    public static function create(): static
    {
        return new static();
    }

    public function identifier(): string
    {
        return static::class;
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        return new FileDiscoverCacheDriver(
            directory: Modules::basePath('bootstrap/cache'),
            serialize: false,
            filename: sprintf(
                'modules-%s.php',
                $this->asset()->value,
            ),
        );
    }

    public function get(): array
    {
        if ($this->isCached()) {
            return $this->cacheDriver()->get($this->identifier());
        }

        return $this->getWithoutCache();
    }

    /**
     * @return Collection<int, array{module: string, path?: string, namespace?: string}>
     */
    public function collect(): Collection
    {
        return collect($this->get());
    }

    public function cache(): array
    {
        $structures = $this->getWithoutCache();

        $this->cacheDriver()->put(
            $this->identifier(),
            $structures
        );

        return $structures;
    }

    public function clear(): static
    {
        $this->cacheDriver()->forget($this->identifier());

        return $this;
    }

    public function isCached(): bool
    {
        return $this->cacheDriver()->has($this->identifier());
    }

    protected function patterns(): array
    {
        return collect($this->asset()->patterns())
            ->map(Modules::modulesPath(...))
            ->toArray();
    }

    public function transform(array $result): array
    {
        return collect($result)
            ->map(
                function (string $item) {
                    if ((is_dir($item) || is_file($item)) && ! class_exists($item)) {
                        return [
                            'module' => Modules::moduleNameFromPath($item),
                            'path' => realpath($item),
                        ];
                    }

                    return [
                        'module' => Modules::moduleNameFromNamespace($item),
                        'namespace' => $item,
                    ];
                }
            )
            ->sortBy(
                fn (array $asset) => (int) (config('modules.modules', [])[$asset['module']]['order'] ?? 9999)
            )
            ->filter(
                fn (array $asset) => config('modules.modules', [])[$asset['module']]['active'] ?? true
            )
            ->values()
            ->toArray();
    }

    abstract public function asset(): AssetType;

    abstract public function getWithoutCache(): array;
}
