<?php

namespace Mozex\Modules\Contracts;

use Illuminate\Support\Collection;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

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
            directory: $this->cachePath(),
            serialize: false,
            filename: $this->cacheFile(),
        );
    }

    public function cachePath(): string
    {
        return Modules::basePath('bootstrap/cache');
    }

    public function cacheFile(): string
    {
        return sprintf(
            'modules-%s.php',
            $this->asset()->value,
        );
    }

    public function get(): array
    {
        if ($this->asset()->isDeactive()) {
            return [];
        }

        if ($this->isCached()) {
            return $this->cacheDriver()->get(
                $this->identifier()
            );
        }

        return $this->getWithoutCache();
    }

    /**
     * @return Collection<int, array{module: string, path: string, namespace?: string}>
     */
    public function collect(): Collection
    {
        return collect($this->get());
    }

    public function cache(): array
    {
        if ($this->asset()->isDeactive()) {
            return [];
        }

        $structures = $this->getWithoutCache();

        $this->cacheDriver()->put(
            $this->identifier(),
            $structures
        );

        return $structures;
    }

    public function clear(): static
    {
        $this->cacheDriver()->forget(
            $this->identifier()
        );

        return $this;
    }

    public function isCached(): bool
    {
        return $this->cacheDriver()->has(
            $this->identifier()
        );
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
                fn (string|DiscoveredClass $item) => $item instanceof DiscoveredClass
                    ? [
                        'module' => Modules::moduleNameFromNamespace($item->namespace),
                        'path' => realpath($item->file),
                        'namespace' => $item->getFcqn(),
                    ]
                    : [
                        'module' => Modules::moduleNameFromPath($item),
                        'path' => realpath($item),
                        'namespace' => str(is_dir($item) ? $item : dirname($item))
                            ->after(Modules::basePath())
                            ->replace(['\\', '/'], ['/', '\\'])
                            ->toString(),
                    ]
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
