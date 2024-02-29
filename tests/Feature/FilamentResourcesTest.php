<?php

use App\Filament\Admin\Resources\TestResource;
use App\Filament\Dashboard\Resources\NestedTestResource;
use Filament\Facades\Filament;
use Modules\First\Filament\Admin\Resources\UserResource;
use Modules\First\Filament\Dashboard\Resources\NestedUserResource;
use Modules\Second\Filament\Admin\Resources\TeamResource;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\FilamentResourcesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::FilamentResources->value.'.active',
        false
    );

    $discoverer = FilamentResourcesScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = FilamentResourcesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace', 'panel'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Admin/Resources')))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Dashboard/Resources')))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Admin/Resources')))
        ->not->toContain(realpath(Modules::modulesPath('Second/Filament/Dashboard/Resources')))
        ->and($collection->pluck('panel')->unique())
        ->toContain('admin')
        ->toContain('dashboard');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register filament resources', function (bool $cache) {
    $discoverer = FilamentResourcesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Filament::getPanel('admin')->getResources())
        ->toHaveCount(3)
        ->toContain(TestResource::class)
        ->toContain(TeamResource::class)
        ->toContain(UserResource::class)
        ->and(Filament::getPanel('dashboard')->getResources())
        ->toHaveCount(2)
        ->toContain(NestedTestResource::class)
        ->toContain(NestedUserResource::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
