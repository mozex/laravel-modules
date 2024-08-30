<?php

use Livewire\Mechanisms\ComponentRegistry;
use Modules\First\Livewire\Teams;
use Modules\Second\Livewire\ListUsers;
use Modules\Second\Livewire\WrongComponents;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportLivewire\LivewireComponentsScout;

test('scout will not collect when disabled', function () {
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

test('discovering will work', function (bool $cache) {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(Teams::class)
        ->toContain(ListUsers::class)
        ->not->toContain(WrongComponents::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register livewire components', function (bool $cache) {
    $discoverer = LivewireComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $components = app(ComponentRegistry::class);

    $discoverer->collect()
        ->each(function (array $asset) use ($components) {
            expect($components->getName($asset['namespace']))->not->toBeNull();
        });

    expect(Blade::render(
        string: '<livewire:first::teams/>',
        deleteCachedView: true
    ))
        ->toContain('Teams Livewire Component')
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
        ->toContain('Nested Users Livewire Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
