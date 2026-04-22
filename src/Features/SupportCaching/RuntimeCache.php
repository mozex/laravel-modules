<?php

namespace Mozex\Modules\Features\SupportCaching;

use Mozex\Modules\Contracts\BaseScout;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * Runtime cache configuration for scout discovery results.
 *
 * Registers the default tiered cache driver factory so every BaseScout gets
 * a static (in-memory) layer over a file (persistent) layer at `cachePath()`.
 *
 * Behavior by environment:
 *  - **Production/local dev**: deploy-time `php artisan modules:cache` writes
 *    to `cachePath()`; every subsequent request reads the file once, then hits
 *    the in-memory layer on each subsequent scout access in the same process.
 *  - **CI**: add `php artisan modules:cache` as a step before running tests.
 *    Workers read the pre-built file at `cachePath()` on first access.
 *  - **Local testing**: no pre-built cache. Scouts discover on first access
 *    and auto-populate the static layer via `BaseScout::get()`, so subsequent
 *    accesses in the same worker are instant. Cross-worker re-discovery is
 *    accepted as the correctness/simplicity trade.
 */
class RuntimeCache
{
    public static function install(): void
    {
        BaseScout::useCacheDriverFactory(
            fn (BaseScout $scout): TieredDiscoverCacheDriver => new TieredDiscoverCacheDriver(
                static: new StaticDiscoverCacheDriver,
                persistent: new FileDiscoverCacheDriver(
                    directory: $scout->cachePath(),
                    serialize: false,
                    filename: $scout->cacheFile(),
                ),
            )
        );
    }
}
