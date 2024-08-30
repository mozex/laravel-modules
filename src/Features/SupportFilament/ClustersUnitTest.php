<?php

use App\Filament\Dashboard\Clusters\Test;
use Filament\Facades\Filament;
use Modules\First\Filament\Admin\Resources\UserResource;
use Modules\First\Filament\Dashboard\Clusters\Users;
use Modules\First\Filament\Dashboard\Resources\NestedUserResource;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportFilament\FilamentClustersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::FilamentClusters->value.'.active',
        false
    );

    $discoverer = FilamentClustersScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = FilamentClustersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace', 'panel'])
        ->and($collection->pluck('path'))
        ->not->toContain(realpath(Modules::modulesPath('First/Filament/Admin/Clusters')))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Dashboard/Clusters')))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Admin/Clusters')))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Dashboard/Clusters')))
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
    $discoverer = FilamentClustersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Filament::getPanel('admin')->getClusterDirectories())
        ->toHaveCount(2)
        ->toContain(Modules::basePath('app/Filament/Admin/Clusters'))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Admin/Clusters')))
        ->and(Filament::getPanel('admin')->getClusteredComponents())
        ->toHaveCount(1)
        ->toHaveKey(Test::class)
        ->and(array_values(Filament::getPanel('admin')->getClusteredComponents())[0])
        ->toContain(UserResource::class)
        ->and(Filament::getPanel('admin')->getClusterNamespaces())
        ->toHaveCount(2)
        ->toContain('App\\Filament\\Admin\\Clusters')
        ->toContain('Modules\\Second\\Filament\\Admin\\Clusters')
        ->and(Filament::getPanel('dashboard')->getClusterDirectories())
        ->toHaveCount(3)
        ->toContain(Modules::basePath('app/Filament/Dashboard/Clusters'))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Dashboard/Clusters')))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Dashboard/Clusters')))
        ->and(Filament::getPanel('dashboard')->getClusteredComponents())
        ->toHaveCount(1)
        ->toHaveKey(Users::class)
        ->and(array_values(Filament::getPanel('dashboard')->getClusteredComponents())[0])
        ->toContain(NestedUserResource::class)
        ->and(Filament::getPanel('dashboard')->getClusterNamespaces())
        ->toHaveCount(3)
        ->toContain('App\\Filament\\Dashboard\\Clusters')
        ->toContain('Modules\\Second\\Filament\\Dashboard\\Clusters')
        ->toContain('Modules\\First\\Filament\\Dashboard\\Clusters');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
