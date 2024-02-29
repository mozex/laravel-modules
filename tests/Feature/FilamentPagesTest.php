<?php

use App\Filament\Admin\Clusters\Settings;
use App\Filament\Admin\Pages\SettingsPage;
use App\Filament\Dashboard\Clusters\Test;
use App\Filament\Dashboard\Pages\LoginPage;
use Filament\Facades\Filament;
use Filament\Pages\Dashboard;
use Modules\First\Filament\Admin\Pages\FirstPage;
use Modules\First\Filament\Dashboard\Clusters\Users;
use Modules\Second\Filament\Admin\Clusters\Team;
use Modules\Second\Filament\Dashboard\Clusters\NestedTeam;
use Modules\Second\Filament\Dashboard\Pages\CreateTeamPage;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\FilamentPagesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::FilamentPages->value.'.active',
        false
    );

    $discoverer = FilamentPagesScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = FilamentPagesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace', 'panel'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Admin/Pages')))
        ->not->toContain(realpath(Modules::modulesPath('First/Filament/Dashboard/Pages')))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Dashboard/Pages')))
        ->not->toContain(realpath(Modules::modulesPath('Second/Filament/Admin/Pages')))
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

it('can register filament pages', function (bool $cache) {
    $discoverer = FilamentPagesScout::create();

    if ($cache) {
        $discoverer->cache();
    }
    ray(Filament::getPanel('dashboard')->getPages());
    expect(Filament::getPanel('admin')->getPages())
        ->toHaveCount(5)
        ->toContain(Dashboard::class)
        ->toContain(SettingsPage::class)
        ->toContain(FirstPage::class)
        ->toContain(Settings::class)
        ->toContain(Team::class)
        ->and(Filament::getPanel('dashboard')->getPages())
        ->toHaveCount(6)
        ->toContain(Dashboard::class)
        ->toContain(LoginPage::class)
        ->toContain(CreateTeamPage::class)
        ->toContain(Test::class)
        ->toContain(NestedTeam::class)
        ->toContain(Users::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
