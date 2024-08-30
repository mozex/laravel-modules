<?php

use App\Filament\Admin\Widgets\SettingsOverviewWidget;
use App\Filament\Dashboard\Widgets\LoginChartWidget;
use Filament\Facades\Filament;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Modules\First\Filament\Admin\Widgets\FirstOverviewWidget;
use Modules\First\Filament\Dashboard\Widgets\RegisterChartWidget;
use Modules\Second\Filament\Admin\Widgets\TeamOverviewWidget;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportFilament\FilamentWidgetsScout;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::FilamentWidgets->value.'.active',
        false
    );

    $discoverer = FilamentWidgetsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = FilamentWidgetsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace', 'panel'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Admin/Widgets')))
        ->toContain(realpath(Modules::modulesPath('First/Filament/Dashboard/Widgets')))
        ->toContain(realpath(Modules::modulesPath('Second/Filament/Admin/Widgets')))
        ->not->toContain(realpath(Modules::modulesPath('Second/Filament/Dashboard/Widgets')))
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

it('can register filament pages', function (bool $cache): void {
    $discoverer = FilamentWidgetsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Filament::getPanel('admin')->getWidgets())
        ->toHaveCount(5)
        ->toContain(AccountWidget::class)
        ->toContain(FilamentInfoWidget::class)
        ->toContain(SettingsOverviewWidget::class)
        ->toContain(TeamOverviewWidget::class)
        ->toContain(FirstOverviewWidget::class)
        ->and(Filament::getPanel('dashboard')->getWidgets())
        ->toHaveCount(4)
        ->toContain(AccountWidget::class)
        ->toContain(FilamentInfoWidget::class)
        ->toContain(LoginChartWidget::class)
        ->toContain(RegisterChartWidget::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
