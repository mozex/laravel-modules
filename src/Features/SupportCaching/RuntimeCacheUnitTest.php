<?php

use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Features\SupportCaching\RuntimeCache;
use Mozex\Modules\Features\SupportCaching\TieredDiscoverCacheDriver;
use Mozex\Modules\Features\SupportConfigs\ConfigsScout;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * Read the (protected) `persistent` cache layer from a TieredDiscoverCacheDriver.
 */
function persistentLayerOf(TieredDiscoverCacheDriver $driver): DiscoverCacheDriver
{
    $property = (new ReflectionClass($driver))->getProperty('persistent');
    $property->setAccessible(true);

    return $property->getValue($driver);
}

afterEach(function (): void {
    // Keep scout singletons isolated between tests so cached driver instances
    // don't leak across assertions.
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();
});

// ---------------------------------------------------------------------------
// RuntimeCache::install — driver factory registration
// ---------------------------------------------------------------------------

it('installs a tiered cache driver factory on BaseScout', function (): void {
    RuntimeCache::install(app());

    $scout = ConfigsScout::create();

    expect($scout->cacheDriver())
        ->toBeInstanceOf(TieredDiscoverCacheDriver::class);
});

it('points the tiered persistent layer at cachePath', function (): void {
    RuntimeCache::install(app());

    $scout = ConfigsScout::create();
    $persistent = persistentLayerOf($scout->cacheDriver());

    expect($persistent)->toBeInstanceOf(FileDiscoverCacheDriver::class)
        ->and($persistent->directory)->toBe(rtrim($scout->cachePath(), '/'));
});

// ---------------------------------------------------------------------------
// BaseScout — factory API
// ---------------------------------------------------------------------------

it('useCacheDriverFactory replaces the default driver for scouts', function (): void {
    $custom = new StaticDiscoverCacheDriver;

    BaseScout::useCacheDriverFactory(fn () => $custom);
    BaseScout::clearInstances();

    expect(ConfigsScout::create()->cacheDriver())->toBe($custom);

    // Re-install the production factory so the rest of the suite works as expected.
    RuntimeCache::install(app());
});

it('useCacheDriverFactory(null) falls back to the default FileDiscoverCacheDriver', function (): void {
    BaseScout::useCacheDriverFactory(null);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();

    expect($scout->cacheDriver())->toBeInstanceOf(FileDiscoverCacheDriver::class);

    RuntimeCache::install(app());
});

it('passes the scout instance into the factory closure', function (): void {
    $received = null;

    BaseScout::useCacheDriverFactory(function (BaseScout $scout) use (&$received) {
        $received = $scout;

        return new StaticDiscoverCacheDriver;
    });
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $scout->cacheDriver();

    expect($received)->toBe($scout);

    RuntimeCache::install(app());
});

it('caches the resolved driver per scout instance', function (): void {
    $scout = ConfigsScout::create();

    expect($scout->cacheDriver())->toBe($scout->cacheDriver());
});

// ---------------------------------------------------------------------------
// BaseScout — get() auto-populate on cache miss
// ---------------------------------------------------------------------------

it('get() populates the active cache driver after discovery', function (): void {
    $static = new StaticDiscoverCacheDriver;

    BaseScout::useCacheDriverFactory(fn () => $static);
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();

    $scout = ConfigsScout::create();

    expect($static->has($scout->identifier()))->toBeFalse();

    $scout->get();

    expect($static->has($scout->identifier()))->toBeTrue();

    RuntimeCache::install(app());
});

// ---------------------------------------------------------------------------
// BaseScout — cache() writes through the active driver's persistent layer
// ---------------------------------------------------------------------------

it('cache() persists through the tiered driver to the cachePath file', function (): void {
    $scout = ConfigsScout::create();

    $cacheFilePath = rtrim($scout->cachePath(), '/').'/'.$scout->cacheFile();
    @unlink($cacheFilePath);

    $scout->cache();

    expect(file_exists($cacheFilePath))->toBeTrue();

    @unlink($cacheFilePath);
});

// ---------------------------------------------------------------------------
// BaseScout — clear() removes the persistent file and clears the driver
// ---------------------------------------------------------------------------

it('clear() removes the active driver persistent file and the in-memory layer', function (): void {
    $scout = ConfigsScout::create();
    $cacheFilePath = rtrim($scout->cachePath(), '/').'/'.$scout->cacheFile();

    $scout->cache();
    expect(file_exists($cacheFilePath))->toBeTrue();

    $scout->clear();

    expect(file_exists($cacheFilePath))->toBeFalse();
});
