<?php

use Mozex\Modules\Features\SupportCaching\TieredDiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\DiscoverCacheDriver;
use Spatie\StructureDiscoverer\Cache\StaticDiscoverCacheDriver;

/**
 * A self-contained in-memory cache driver, independent of the shared-static
 * `StaticDiscoverCacheDriver`, so we can stand up an isolated persistent layer
 * for each test and observe it separately from the static layer.
 */
function makeInMemoryDriver(): DiscoverCacheDriver
{
    return new class implements DiscoverCacheDriver
    {
        /** @var array<string, array<mixed>> */
        public array $entries = [];

        public function has(string $id): bool
        {
            return array_key_exists($id, $this->entries);
        }

        /** @return array<mixed> */
        public function get(string $id): array
        {
            return $this->entries[$id];
        }

        /** @param  array<mixed>  $discovered */
        public function put(string $id, array $discovered): void
        {
            $this->entries[$id] = $discovered;
        }

        public function forget(string $id): void
        {
            unset($this->entries[$id]);
        }
    };
}

beforeEach(function (): void {
    StaticDiscoverCacheDriver::clear();

    $this->static = new StaticDiscoverCacheDriver;
    $this->persistent = makeInMemoryDriver();
    $this->driver = new TieredDiscoverCacheDriver(
        static: $this->static,
        persistent: $this->persistent,
    );
});

it('reports has() true when key is only in the static layer', function (): void {
    $this->static->put('id', ['static']);

    expect($this->driver->has('id'))->toBeTrue();
});

it('reports has() true when key is only in the persistent layer', function (): void {
    $this->persistent->put('id', ['persistent']);

    expect($this->driver->has('id'))->toBeTrue();
});

it('reports has() false when key is in neither layer', function (): void {
    expect($this->driver->has('missing'))->toBeFalse();
});

it('get() returns the static value when present', function (): void {
    $this->static->put('id', ['from static']);
    $this->persistent->put('id', ['from persistent']);

    expect($this->driver->get('id'))->toBe(['from static']);
});

it('get() falls back to the persistent layer on static miss', function (): void {
    $this->persistent->put('id', ['from persistent']);

    expect($this->driver->get('id'))->toBe(['from persistent']);
});

it('get() warms the static layer when read-through hits persistent', function (): void {
    $this->persistent->put('id', ['loaded']);

    expect($this->static->has('id'))->toBeFalse();

    $this->driver->get('id');

    expect($this->static->has('id'))->toBeTrue()
        ->and($this->static->get('id'))->toBe(['loaded']);
});

it('put() writes to the static layer only', function (): void {
    $this->driver->put('id', ['new']);

    expect($this->static->has('id'))->toBeTrue()
        ->and($this->static->get('id'))->toBe(['new']);
});

it('put() never mutates the persistent layer', function (): void {
    $this->driver->put('id', ['new']);

    expect($this->persistent->has('id'))->toBeFalse();
});

it('forget() removes from both layers', function (): void {
    $this->static->put('id', ['staticval']);
    $this->persistent->put('id', ['persistentval']);

    $this->driver->forget('id');

    expect($this->static->has('id'))->toBeFalse()
        ->and($this->persistent->has('id'))->toBeFalse();
});

it('forget() is idempotent on missing keys', function (): void {
    // Should not throw even if the key was never written.
    $this->driver->forget('never-written');

    expect($this->static->has('never-written'))->toBeFalse()
        ->and($this->persistent->has('never-written'))->toBeFalse();
});

it('subsequent get() after warming hits the static layer without touching persistent again', function (): void {
    $this->persistent->put('id', ['once']);

    $this->driver->get('id'); // warm

    // Mutate persistent; static still has warm value, so we should see warm value.
    $this->persistent->put('id', ['changed']);

    expect($this->driver->get('id'))->toBe(['once']);
});

it('persist() writes through to both the static and persistent layers', function (): void {
    $this->driver->persist('id', ['both']);

    expect($this->static->has('id'))->toBeTrue()
        ->and($this->static->get('id'))->toBe(['both'])
        ->and($this->persistent->has('id'))->toBeTrue()
        ->and($this->persistent->get('id'))->toBe(['both']);
});

it('implements the Persistable interface', function (): void {
    expect($this->driver)->toBeInstanceOf(\Mozex\Modules\Features\SupportCaching\Persistable::class);
});
