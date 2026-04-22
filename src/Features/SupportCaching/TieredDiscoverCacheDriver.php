<?php

namespace Mozex\Modules\Features\SupportCaching;

use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * Two-layer cache driver: an in-memory static layer in front of any persistent driver.
 *
 * - `get()` checks the static layer first; on miss, read-through loads from the
 *   persistent driver and warms the static layer so subsequent reads in the
 *   same process are O(1).
 * - `put()` only populates the static layer. This keeps runtime auto-populate
 *   paths (like `BaseScout::get()` on a cache miss) from silently writing disk
 *   files behind the user's back.
 * - `persist()` is the explicit "write through to both layers" operation —
 *   used by `BaseScout::cache()` (the `php artisan modules:cache` code path).
 * - `forget()` clears both layers. Asymmetric with `put()` by design: runtime
 *   writes stay in-memory; explicit clears remove everything.
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
        // and updated exclusively through the explicit `persist()` path (i.e.
        // `php artisan modules:cache`). This prevents runtime auto-populate
        // from silently creating disk cache files.
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
     * Callers that genuinely want to persist to the file layer — such as
     * `BaseScout::cache()` invoked by `php artisan modules:cache` — use this
     * method instead.
     *
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void
    {
        $this->static->put($id, $discovered);
        $this->persistent->put($id, $discovered);
    }
}
