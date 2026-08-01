<?php

use Illuminate\Support\Facades\Blade;
use Modules\First\View\Components\Filter;
use Modules\First\View\Components\WrongComponent;
use Modules\Second\View\Components\Button\Loading;
use Modules\Second\View\Components\Search;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportBladeComponents\BladeComponentsScout;
use Mozex\Modules\Features\SupportBladeComponents\BladeComponentsServiceProvider;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::BladeComponents->value.'.active',
        false
    );

    $discoverer = BladeComponentsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = BladeComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(Filter::class)
        ->toContain(Search::class)
        ->toContain(Loading::class)
        ->not->toContain(WrongComponent::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

test('discovered assets include a precomputed alias', function (): void {
    $collection = collect(BladeComponentsScout::create()->getWithoutCache());

    expect($collection)
        ->each->toHaveKey('alias')
        ->and($collection->firstWhere('namespace', Filter::class)['alias'])
        ->toBe('first::filter')
        ->and($collection->firstWhere('namespace', Loading::class)['alias'])
        ->toBe('second::button.loading');
});

it('registers aliases from the cache payload', function (): void {
    $scout = BladeComponentsScout::create();

    $tampered = collect($scout->getWithoutCache())
        ->map(fn (array $asset): array => [
            ...$asset,
            'alias' => 'tampered::'.class_basename($asset['namespace']),
        ])
        ->all();

    $scout->cacheDriver()->put($scout->identifier(), $tampered);

    try {
        (new BladeComponentsServiceProvider(app()))->boot();

        expect(Blade::getClassComponentAliases())
            ->toHaveKey('tampered::'.class_basename(Filter::class));
    } finally {
        $scout->clear();
    }
});

it('falls back to computing aliases for cache payloads without an alias key', function (): void {
    $scout = BladeComponentsScout::create();

    $legacy = collect($scout->getWithoutCache())
        ->map(function (array $asset): array {
            unset($asset['alias']);

            return $asset;
        })
        ->all();

    $scout->cacheDriver()->put($scout->identifier(), $legacy);

    $warnings = [];

    set_error_handler(function (int $errno, string $errstr) use (&$warnings): bool {
        if (str_contains($errstr, 'Undefined')) {
            $warnings[] = $errstr;
        }

        return true;
    }, E_WARNING | E_NOTICE);

    try {
        (new BladeComponentsServiceProvider(app()))->boot();
    } finally {
        restore_error_handler();
        $scout->clear();
    }

    expect($warnings)->toBeEmpty()
        ->and(Blade::getClassComponentAliases())
        ->toHaveKey('first::filter');
});

it('can load blade components', function (bool $cache): void {
    $discoverer = BladeComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $components = Blade::getClassComponentAliases();

    $discoverer->collect()
        ->each(function (array $asset) use ($components): void {
            expect($components)->toContain($asset['namespace']);
        });

    expect(Blade::render(
        string: '<x-first::filter name="Filter"/>',
        deleteCachedView: true
    ))
        ->toContain('Filter Component')
        ->and(Blade::render(
            string: '<x-first::select name="Select"/>',
            deleteCachedView: true
        ))
        ->toContain('Select Component')
        ->and(Blade::render(
            string: '<x-first::without-view name="Without View"/>',
            deleteCachedView: true
        ))
        ->toContain('Without View Component')
        ->and(Blade::render(
            string: '<x-second::search name="Search"/>',
            deleteCachedView: true
        ))
        ->toContain('Search Component')
        ->and(Blade::render(
            string: '<x-second::button.loading name="Loading"/>',
            deleteCachedView: true
        ))
        ->toContain('Loading Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
