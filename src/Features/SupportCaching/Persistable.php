<?php

namespace Mozex\Modules\Features\SupportCaching;

/**
 * Capability interface for cache drivers that support explicit "write through"
 * to a persistent backing store, as distinct from in-memory population via put().
 */
interface Persistable
{
    /**
     * @param  array<mixed>  $discovered
     */
    public function persist(string $id, array $discovered): void;
}
