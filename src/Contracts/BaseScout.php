<?php

namespace Mozex\Modules\Contracts;

use Closure;
use Illuminate\Support\Collection;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportCaching\Persistable;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;

abstract class BaseScout
{
    /** @var array<class-string, static> */
    protected static array $instances = [];

    /** @var (Closure(self): DiscoverCacheDriver)|null */
    protected static ?Closure $cacheDriverFactory = null;

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

    /**
     * Register a custom cache driver factory for every scout, or clear it (null)
     * to fall back to the default `FileDiscoverCacheDriver`.
     *
     * Note: scouts cache their resolved driver in `$cacheDriverInstance` per
     * instance. If any scout singletons have already resolved their driver
     * before you call this, also call `BaseScout::clearInstances()` so the next
     * scout access picks up the new factory.
     *
     * `self::` is deliberate so the single `BaseScout::$cacheDriverFactory`
     * slot is always targeted, regardless of which subclass invokes the call.
     *
     * @param  (Closure(self): DiscoverCacheDriver)|null  $factory
     */
    public static function useCacheDriverFactory(?Closure $factory): void
    {
        self::$cacheDriverFactory = $factory;
    }

    public function identifier(): string
    {
        return static::class;
    }

    public function cacheDriver(): DiscoverCacheDriver
    {
        if ($this->cacheDriverInstance !== null) {
            return $this->cacheDriverInstance;
        }

        if (self::$cacheDriverFactory instanceof Closure) {
            return $this->cacheDriverInstance = (self::$cacheDriverFactory)($this);
        }

        return $this->cacheDriverInstance = new FileDiscoverCacheDriver(
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

        $discovered = $this->getWithoutCache();

        // Populate the active cache so subsequent accesses in this process
        // are served from memory. Tiered drivers write only to the in-memory
        // layer here — file persistence remains explicit via `modules:cache`.
        $this->cacheDriver()->put(
            $this->identifier(),
            $discovered
        );

        return $discovered;
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

        $driver = $this->cacheDriver();

        // Drivers may suppress writes-to-disk during runtime auto-populate — the
        // tiered driver, for instance, only touches its in-memory layer on
        // `put()`. `cache()` is the explicit persistence call, so drivers that
        // advertise `Persistable` get their `persist()` method (write through to
        // all layers) instead of `put()`.
        if ($driver instanceof Persistable) {
            $driver->persist($this->identifier(), $structures);
        } else {
            $driver->put($this->identifier(), $structures);
        }

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
