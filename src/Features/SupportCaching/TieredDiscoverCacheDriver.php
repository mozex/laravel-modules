<?php

namespace Mozex\Modules\Features\SupportCaching;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * Two-layer cache driver: an in-memory static layer in front of any persistent driver.
 *
 * - get() reads from the static layer, falling back to the persistent driver
 *   and warming the static layer on a miss.
 * - put() populates the static layer only — runtime auto-populate never
 *   touches disk.
 * - persist() writes through to both layers. This is the explicit persistence
 *   path for callers that want the result kept on disk.
 * - forget() clears both layers.
 */
class TieredDiscoverCacheDriver implements DiscoverCacheDriver, Persistable
{
    public function __construct(
        protected StaticDiscoverCacheDriver $static,
        protected DiscoverCacheDriver $persistent,
    ) {}

    public function has(string $id): bool
    {
        if ($this->static->has($id)) {
            return true;
        }

        return $this->persistent->has($id);
    }

    /**
     * @return array<mixed>
     */
    public function get(string $id): array
    {
        if ($this->static->has($id)) {
            return $this->static->get($id);
        }

        $discovered = $this->persistent->get($id);

        $this->static->put($id, $discovered);

        return $discovered;
    }

    /**
     * @param  array<mixed>  $discovered
     */
    public function put(string $id, array $discovered): void
    {
        $this->static->put($id, $discovered);
    }

    public function forget(string $id): void
    {
        $this->static->forget($id);
        $this->persistent->forget($id);
    }

    /**
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void
    {
        $this->static->put($id, $discovered);
        $this->persistent->put($id, $discovered);
    }
}
