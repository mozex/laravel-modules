<?php

use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Features\SupportCaching\RuntimeCache;
use Mozex\Modules\Features\SupportCaching\TieredDiscoverCacheDriver;
use Mozex\Modules\Features\SupportConfigs\ConfigsScout;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\FileDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * End-to-end BaseScout workflow tests.
 *
 * Each scenario configures a specific driver on the scout (static-only,
 * file-only, or tiered) and verifies the full cache lifecycle:
 * discovery → auto-populate on get() → cache hit on second get() →
 * persist via cache() → clear() removes everything.
 */
function driverScenarios(): array
{
    return [
        'static only' => [
            fn (BaseScout $scout) => new StaticDiscoverCacheDriver,
        ],
        'file only' => [
            fn (BaseScout $scout) => new FileDiscoverCacheDriver(
                directory: sys_get_temp_dir().'/modules-scout-workflow-'.getmypid(),
                serialize: false,
                filename: $scout->cacheFile(),
            ),
        ],
        'tiered (static + file)' => [
            fn (BaseScout $scout) => new TieredDiscoverCacheDriver(
                static: new StaticDiscoverCacheDriver,
                persistent: new FileDiscoverCacheDriver(
                    directory: sys_get_temp_dir().'/modules-scout-workflow-tiered-'.getmypid(),
                    serialize: false,
                    filename: $scout->cacheFile(),
                ),
            ),
        ],
    ];
}

beforeEach(function (): void {
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();

    foreach (['workflow', 'workflow-tiered'] as $suffix) {
        $dir = sys_get_temp_dir().'/modules-scout-'.$suffix.'-'.getmypid();
        if (is_dir($dir)) {
            foreach ((array) glob($dir.'/*') as $file) {
                @unlink($file);
            }
            @rmdir($dir);
        }
    }
});

afterEach(function (): void {
    // Restore production factory so other suites aren't left with a stale scout driver.
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();
    RuntimeCache::install();
});

/*
|--------------------------------------------------------------------------
| End-to-end: discovery → auto-populate → cache hit
|--------------------------------------------------------------------------
*/

it('discovers on first access and serves subsequent reads from cache', function (Closure $factory): void {
    BaseScout::useCacheDriverFactory($factory);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();

    expect($scout->isCached())->toBeFalse();

    $first = $scout->get();

    expect($first)->not->toBeEmpty()
        ->and($scout->isCached())->toBeTrue();

    // Subsequent reads come from the cache — should return identical data.
    expect($scout->get())->toBe($first);
})->with(driverScenarios());

/*
|--------------------------------------------------------------------------
| cache() persists and isCached() reflects it
|--------------------------------------------------------------------------
*/

it('cache() persists discovery so a fresh scout instance reports isCached', function (Closure $factory): void {
    BaseScout::useCacheDriverFactory($factory);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $scout->cache();

    // A fresh scout sees the same persisted state (file-based drivers).
    BaseScout::clearInstances();
    $fresh = ConfigsScout::create();

    // For file/tiered: the persistent file makes isCached true across instances.
    // For static-only: state is shared via the static driver's process-level entries.
    expect($fresh->isCached())->toBeTrue()
        ->and($fresh->get())->not->toBeEmpty();
})->with([
    'file only' => [
        fn (BaseScout $scout) => new FileDiscoverCacheDriver(
            directory: sys_get_temp_dir().'/modules-scout-workflow-'.getmypid(),
            serialize: false,
            filename: $scout->cacheFile(),
        ),
    ],
    'tiered (static + file)' => [
        fn (BaseScout $scout) => new TieredDiscoverCacheDriver(
            static: new StaticDiscoverCacheDriver,
            persistent: new FileDiscoverCacheDriver(
                directory: sys_get_temp_dir().'/modules-scout-workflow-tiered-'.getmypid(),
                serialize: false,
                filename: $scout->cacheFile(),
            ),
        ),
    ],
]);

/*
|--------------------------------------------------------------------------
| clear() removes everything the driver can see
|--------------------------------------------------------------------------
*/

it('clear() removes cached data across all driver types', function (Closure $factory): void {
    BaseScout::useCacheDriverFactory($factory);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();

    $scout->get(); // triggers discovery + auto-populate
    expect($scout->isCached())->toBeTrue();

    $scout->clear();

    expect($scout->isCached())->toBeFalse();
})->with(driverScenarios());

/*
|--------------------------------------------------------------------------
| Driver-specific behavior through the scout
|--------------------------------------------------------------------------
*/

it('static-only driver keeps cache in memory and survives across scout instances in the same process', function (): void {
    BaseScout::useCacheDriverFactory(fn () => new StaticDiscoverCacheDriver);
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();

    ConfigsScout::create()->get();

    // New scout instance: static cache is process-wide, so still cached.
    BaseScout::clearInstances();
    expect(ConfigsScout::create()->isCached())->toBeTrue();
});

it('file-only driver persists the cache to disk at the configured directory', function (): void {
    $directory = sys_get_temp_dir().'/modules-scout-workflow-'.getmypid();

    BaseScout::useCacheDriverFactory(
        fn (BaseScout $scout) => new FileDiscoverCacheDriver(
            directory: $directory,
            serialize: false,
            filename: $scout->cacheFile(),
        )
    );
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $scout->cache();

    $expectedFile = $directory.'/'.$scout->cacheFile();

    expect(file_exists($expectedFile))->toBeTrue();
});

it('tiered driver writes get() auto-populate to static only (never disk)', function (): void {
    $directory = sys_get_temp_dir().'/modules-scout-workflow-tiered-'.getmypid();

    BaseScout::useCacheDriverFactory(
        fn (BaseScout $scout) => new TieredDiscoverCacheDriver(
            static: new StaticDiscoverCacheDriver,
            persistent: new FileDiscoverCacheDriver(
                directory: $directory,
                serialize: false,
                filename: $scout->cacheFile(),
            ),
        )
    );
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();

    $scout = ConfigsScout::create();
    $scout->get(); // triggers discovery + tiered.put() → static only

    $expectedFile = $directory.'/'.$scout->cacheFile();

    expect(file_exists($expectedFile))->toBeFalse()
        ->and(StaticDiscoverCacheDriver::$entries)->toHaveKey($scout->identifier());
});

it('tiered driver reads persistent file and warms static on first access', function (): void {
    $directory = sys_get_temp_dir().'/modules-scout-workflow-tiered-'.getmypid();
    @mkdir($directory, 0777, true);

    $scoutForFilename = ConfigsScout::create();
    $payload = [['module' => 'First', 'path' => '/tmp/x.php', 'namespace' => 'X\\Y']];

    // Seed the persistent layer directly.
    (new FileDiscoverCacheDriver(
        directory: $directory,
        serialize: false,
        filename: $scoutForFilename->cacheFile(),
    ))->put($scoutForFilename->identifier(), $payload);

    BaseScout::useCacheDriverFactory(
        fn (BaseScout $scout) => new TieredDiscoverCacheDriver(
            static: new StaticDiscoverCacheDriver,
            persistent: new FileDiscoverCacheDriver(
                directory: $directory,
                serialize: false,
                filename: $scout->cacheFile(),
            ),
        )
    );
    BaseScout::clearInstances();
    StaticDiscoverCacheDriver::clear();

    $scout = ConfigsScout::create();

    expect(StaticDiscoverCacheDriver::$entries)->not->toHaveKey($scout->identifier());

    $result = $scout->get();

    expect($result)->toBe($payload)
        ->and(StaticDiscoverCacheDriver::$entries)->toHaveKey($scout->identifier());
});

/*
|--------------------------------------------------------------------------
| Factory API edge cases
|--------------------------------------------------------------------------
*/

it('default factory-less behavior falls back to FileDiscoverCacheDriver at cachePath', function (): void {
    BaseScout::useCacheDriverFactory(null);
    BaseScout::clearInstances();

    $driver = ConfigsScout::create()->cacheDriver();

    expect($driver)->toBeInstanceOf(FileDiscoverCacheDriver::class);
});

it('accepts any DiscoverCacheDriver implementation through the factory', function (): void {
    $custom = new class implements DiscoverCacheDriver
    {
        public array $entries = [];

        public function has(string $id): bool
        {
            return array_key_exists($id, $this->entries);
        }

        public function get(string $id): array
        {
            return $this->entries[$id];
        }

        public function put(string $id, array $discovered): void
        {
            $this->entries[$id] = $discovered;
        }

        public function forget(string $id): void
        {
            unset($this->entries[$id]);
        }
    };

    BaseScout::useCacheDriverFactory(fn () => $custom);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $result = $scout->get();

    expect($scout->cacheDriver())->toBe($custom)
        ->and($custom->entries)->toHaveKey($scout->identifier())
        ->and($custom->entries[$scout->identifier()])->toBe($result);
});

it('cache() routes through persist() for any driver that implements Persistable', function (): void {
    $driver = new class implements DiscoverCacheDriver, \Mozex\Modules\Features\SupportCaching\Persistable
    {
        public array $putCalls = [];

        public array $persistCalls = [];

        public function has(string $id): bool
        {
            return false;
        }

        public function get(string $id): array
        {
            return [];
        }

        public function put(string $id, array $discovered): void
        {
            $this->putCalls[] = $id;
        }

        public function forget(string $id): void {}

        public function persist(string $id, array $discovered): void
        {
            $this->persistCalls[] = $id;
        }
    };

    BaseScout::useCacheDriverFactory(fn () => $driver);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $scout->cache();

    expect($driver->persistCalls)->toBe([$scout->identifier()])
        ->and($driver->putCalls)->toBe([]);
});

it('cache() falls through to put() for drivers that do not implement Persistable', function (): void {
    $driver = new class implements DiscoverCacheDriver
    {
        public array $putCalls = [];

        public function has(string $id): bool
        {
            return false;
        }

        public function get(string $id): array
        {
            return [];
        }

        public function put(string $id, array $discovered): void
        {
            $this->putCalls[] = $id;
        }

        public function forget(string $id): void {}
    };

    BaseScout::useCacheDriverFactory(fn () => $driver);
    BaseScout::clearInstances();

    $scout = ConfigsScout::create();
    $scout->cache();

    expect($driver->putCalls)->toBe([$scout->identifier()]);
});
