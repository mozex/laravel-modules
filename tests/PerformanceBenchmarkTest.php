<?php

use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Enums\AssetType;

beforeEach(function (): void {
    if (! env('RUN_BENCHMARKS')) {
        $this->markTestSkipped('Set RUN_BENCHMARKS=1 to run performance benchmarks.');
    }
});

function measureMedian(Closure $fn, int $iterations): float
{
    $times = [];

    // Warm up
    $fn();

    for ($i = 0; $i < $iterations; $i++) {
        $start = hrtime(true);
        $fn();
        $times[] = (hrtime(true) - $start) / 1e6; // ms
    }

    sort($times);
    $mid = intdiv($iterations, 2);

    return $iterations % 2 === 0
        ? ($times[$mid - 1] + $times[$mid]) / 2
        : $times[$mid];
}

test('benchmark: scout get() with cache', function (): void {
    $results = [];

    AssetType::activeScouts()->each(function (BaseScout $scout) use (&$results): void {
        $scout->cache();

        // Clear the memoized instance so each iteration re-reads from file
        BaseScout::clearInstances();

        $median = measureMedian(function () use ($scout): void {
            $scout->get();
        }, 200);

        $results[$scout->asset()->title()] = round($median, 3);

        $scout->clear();
    });

    dump('=== Scout get() with cache (median ms per call) ===');
    dump($results);

    expect(true)->toBeTrue();
});

test('benchmark: scout get() without cache', function (): void {
    $results = [];

    AssetType::activeScouts()->each(function (BaseScout $scout) use (&$results): void {
        $scout->clear();

        $median = measureMedian(function () use ($scout): void {
            $scout->getWithoutCache();
        }, 100); // Fewer iterations since uncached is slow

        $results[$scout->asset()->title()] = round($median, 3);
    });

    dump('=== Scout getWithoutCache() (median ms per call) ===');
    dump($results);

    expect(true)->toBeTrue();
});

test('benchmark: full module boot with cache', function (): void {
    // First, cache all scouts
    AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->cache());

    $median = measureMedian(function (): void {
        // Simulate what happens during boot: each active scout fetches results
        AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->get());
    }, 200);

    dump('=== Full module boot (all scouts, cached) ===');
    dump(['median_ms' => round($median, 3)]);

    // Clean up
    AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->clear());

    expect(true)->toBeTrue();
});

test('benchmark: full module boot without cache', function (): void {
    // Ensure no cache
    AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->clear());

    $median = measureMedian(function (): void {
        AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->getWithoutCache());
    }, 50); // Very few iterations since uncached is expensive

    dump('=== Full module boot (all scouts, uncached) ===');
    dump(['median_ms' => round($median, 3)]);

    expect(true)->toBeTrue();
});

test('benchmark: repeated scout calls (memoization test)', function (): void {
    AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->cache());

    $withoutMemo = measureMedian(function (): void {
        for ($i = 0; $i < 10; $i++) {
            AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->get());
        }
    }, 50);

    dump('=== Repeated scout calls (50x all scouts, cached) ===');
    dump(['median_ms' => round($withoutMemo, 3)]);

    AssetType::activeScouts()->each(fn (BaseScout $scout) => $scout->clear());

    expect(true)->toBeTrue();
});
