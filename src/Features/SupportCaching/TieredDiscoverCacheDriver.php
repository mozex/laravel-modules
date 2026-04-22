<?php

namespace Mozex\Modules\Features\SupportCaching;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * Two-layer cache driver: an in-memory static layer in front of any persistent driver.
 *
 * - Reads check the static layer first; on miss, read-through loads from the persistent
 *   driver and warms the static layer so future reads in this process are O(1).
 * - Writes only populate the static layer. The persistent layer is treated as read-only
 *   here (owned by `php artisan modules:cache`), so test runs never disrupt its contents.
 */
class TieredDiscoverCacheDriver implements DiscoverCacheDriver, Persistable
{
    public function __construct(
        protected StaticDiscoverCacheDriver $static,
        protected DiscoverCacheDriver $persistent,
    ) {
    }

    public function has(string $id): bool
    {
        return $this->static->has($id) || $this->persistent->has($id);
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

        // Warm the static layer so subsequent accesses in this process are instant.
        $this->static->put($id, $discovered);

        return $discovered;
    }

    /**
     * @param  array<mixed>  $discovered
     */
    public function put(string $id, array $discovered): void
    {
        // Only populate the static layer. The persistent layer is deployer-owned
        // (populated by `modules:cache`) and we intentionally never write to it here
        // to avoid polluting dev/production cache during tests.
        $this->static->put($id, $discovered);
    }

    public function forget(string $id): void
    {
        $this->static->forget($id);
        $this->persistent->forget($id);
    }

    /**
     * Explicit "write through" to both layers.
     *
     * `put()` intentionally only populates the in-memory layer so that runtime
     * scout access (discovery → auto-populate) never creates disk cache files.
     * Callers that genuinely want to persist to the file layer — for example,
     * `php artisan modules:cache` or the test-session coordinator — should use
     * this method instead.
     *
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void
    {
        $this->static->put($id, $discovered);
        $this->persistent->put($id, $discovered);
    }
}
