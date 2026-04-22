<?php

namespace Mozex\Modules\Features\SupportCaching;

/**
 * Capability interface for cache drivers that support explicit "write through"
 * to a persistent backing store.
 *
 * Drivers that cache in memory only (or use `put()` to populate both layers)
 * don't need to implement this. The tiered driver intentionally makes `put()`
 * an in-memory-only operation so runtime auto-populate never creates disk
 * files; explicit persistence goes through `persist()` instead — see
 * `BaseScout::cache()` for the call site.
 */
interface Persistable
{
    /**
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void;
}
