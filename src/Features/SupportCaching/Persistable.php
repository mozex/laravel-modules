<?php

namespace Mozex\Modules\Features\SupportCaching;

/**
 * Capability interface for cache drivers that support explicit "write through"
 * to a persistent backing store.
 *
 * Drivers that cache in memory only (or use `put()` to populate both layers)
 * don't need to implement this. The tiered driver intentionally makes `put()`
 * an in-memory-only operation to prevent runtime auto-populate from creating
 * disk files, so callers that want the result persisted (like `modules:cache`
 * and the test-session coordinator) go through `persist()` instead.
 */
interface Persistable
{
    /**
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void;
}
