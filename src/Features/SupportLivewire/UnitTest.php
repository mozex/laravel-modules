<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportLivewire\LivewireComponentsScout;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::LivewireComponents->value.'.active',
        false
    );

    $discoverer = LivewireComponentsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->toHaveCount(3)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('module')->toArray())
        ->toContain('First')
        ->toContain('Second')
        ->toContain('PWA');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register livewire components', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Blade::render(
        string: '<livewire:first::teams/>',
        deleteCachedView: true
    ))
        ->toContain('Teams Livewire Component')
        ->and(Blade::render(
            string: '<livewire:first::chained/>',
            deleteCachedView: true
        ))
        ->toContain('Chained Livewire Component')
        ->and(Blade::render(
            string: '<livewire:second::list-users/>',
            deleteCachedView: true
        ))
        ->toContain('List Users Livewire Component')
        ->and(Blade::render(
            string: '<livewire:pwa::icons/>',
            deleteCachedView: true
        ))
        ->toContain('PWA Icons Livewire Component')
        ->and(Blade::render(
            string: '<livewire:first::nested.nested-users/>',
            deleteCachedView: true
        ))
        ->toContain('Nested Users Livewire Component')
        ->and(Blade::render(
            string: '<livewire:first::edge-case/>',
            deleteCachedView: true
        ))
        ->toContain('Edge Case Livewire Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register multi-file components', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Blade::render(
        string: '<livewire:first::toggle/>',
        deleteCachedView: true
    ))
        ->toContain('Toggle: Off');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can discover components from multiple view paths', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    // Component from primary view path (Resources/views/livewire)
    expect(Blade::render(
        string: '<livewire:first::counter/>',
        deleteCachedView: true
    ))
        ->toContain('Counter: 2')
        // Component from additional view path (Resources/views/extra-livewire)
        ->and(Blade::render(
            string: '<livewire:first::greeting/>',
            deleteCachedView: true
        ))
        ->toContain('Hello from extra livewire directory');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
])->skip('Livewire addNamespace() only supports one view path per namespace — awaiting upstream support.');

it('can register single-file components', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Blade::render(
        string: '<livewire:first::counter/>',
        deleteCachedView: true
    ))
        ->toContain('Counter: 2');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
