<?php

use Livewire\Livewire;
use Modules\First\Livewire\Teams;
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

it('can resolve components via Livewire::test()', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    // Class-based component by class reference
    Livewire::test(Teams::class)
        ->assertSee('Teams Livewire Component');

    // Class-based component by name
    Livewire::test('first::teams')
        ->assertSee('Teams Livewire Component');

    // Single-file component (SFC)
    Livewire::test('first::counter')
        ->assertSee('Counter:');

    // Multi-file component (MFC)
    Livewire::test('first::toggle')
        ->assertSee('Toggle:');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can resolve components via Route::livewire() with class reference', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $this->get(route('teams'))
        ->assertOk()
        ->assertSee('Teams Livewire Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
])->skip('Livewire::addNamespace() does not currently support class-based registration with Route::livewire() — awaiting upstream support.');

it('can resolve components via Route::livewire() with name string', function (bool $cache): void {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    // Class-based component by name
    $this->get(route('teams-by-name'))
        ->assertOk()
        ->assertSee('Teams Livewire Component');

    // Single-file component (SFC)
    $this->get(route('counter'))
        ->assertOk()
        ->assertSee('Counter:');

    // Multi-file component (MFC)
    $this->get(route('toggle'))
        ->assertOk()
        ->assertSee('Toggle:');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
