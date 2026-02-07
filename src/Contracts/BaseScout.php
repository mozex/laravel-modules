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
    /** @var array<class-string, static> */
    protected static array $instances = [];

    protected ?DiscoverCacheDriver $cacheDriverInstance = null;

    public static function create(): static
    {
        return new static; // @phpstan-ignore-line
    }

    public static function instance(): static
    {
        return self::$instances[static::class] ??= static::create();
    }

    public static function clearInstances(): void
    {
        self::$instances = [];
    }

    public function identifier(): string
    {
        return static::class;
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        return $this->cacheDriverInstance ??= new FileDiscoverCacheDriver(
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

    /**
     * @return array<array-key, array{module: string, path: string, namespace: class-string}>
     */
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
     * @return Collection<array-key, array{module: string, path: string, namespace: class-string}>
     */
    public function collect(): Collection
    {
        return collect($this->get());
    }

    /**
     * @return array<array-key, array{module: string, path: string, namespace: class-string}>
     */
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

    /**
     * @return array<array-key, string>
     */
    protected function patterns(): array
    {
        return collect($this->asset()->patterns())
            ->map(Modules::modulesPath(...))
            ->toArray();
    }

    /**
     * @param  array<array-key, string|DiscoveredClass>  $result
     * @return array<array-key, array{module: string, path: string, namespace: class-string}>
     */
    public function transform(array $result): array
    {
        /** @var array<string, array{active?: bool, order?: int}> $config */
        $config = config('modules.modules', []);

        return collect($result)
            ->map(
                fn (string|DiscoveredClass $item): array => $item instanceof DiscoveredClass
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
                fn (array $asset): int => (int) ($config[$asset['module']]['order'] ?? 9999)
            )
            ->filter(
                fn (array $asset) => $config[$asset['module']]['active'] ?? true
            )
            ->values()
            ->toArray();
    }

    abstract public function asset(): AssetType;

    /**
     * @return array<array-key, mixed>
     */
    abstract public function getWithoutCache(): array;
}
