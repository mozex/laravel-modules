<?php

use Livewire\Mechanisms\ComponentRegistry;
use Modules\First\Livewire\Teams;
use Modules\Second\Livewire\ListUsers;
use Modules\Second\Livewire\WrongComponents;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\LivewireComponentsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::LivewireComponents->value.'.active',
        false
    );

    expect(LivewireComponentsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(LivewireComponentsScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(LivewireComponentsScout::create()->collect()->pluck('namespace'))
        ->toContain(Teams::class)
        ->toContain(ListUsers::class)
        ->not->toContain(WrongComponents::class);
});

it('can register livewire components', function () {
    $components = app(ComponentRegistry::class);

    LivewireComponentsScout::create()->collect()
        ->each(function (array $asset) use ($components) {
            expect($components->getName($asset['namespace']))->not->toBeNull();
        });

    expect(Blade::render(
        string: '<livewire:teams/>',
        deleteCachedView: true
    ))
        ->toContain('Teams Livewire Component')
        ->and(Blade::render(
            string: '<livewire:list-users/>',
            deleteCachedView: true
        ))
        ->toContain('List Users Livewire Component')
        ->and(Blade::render(
            string: '<livewire:nested.nested-users/>',
            deleteCachedView: true
        ))
        ->toContain('Nested Users Livewire Component');
});
