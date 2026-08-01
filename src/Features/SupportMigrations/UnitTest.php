<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportMigrations\MigrationsScout;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::Migrations->value.'.active',
        false
    );

    $discoverer = MigrationsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = MigrationsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Database/Migrations')))
        ->toContain(realpath(Modules::modulesPath('Second/Database/Migrations')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('defers migration path discovery until the migrator resolves', function (): void {
    config()->set(
        'modules.'.AssetType::Migrations->value.'.active',
        false
    );

    expect(app('migrator')->paths())
        ->not->toContain(realpath(Modules::modulesPath('First/Database/Migrations')))
        ->not->toContain(realpath(Modules::modulesPath('Second/Database/Migrations')));
});

it('can load migrations', function (bool $cache): void {
    $discoverer = MigrationsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $migrations = app('migrator')->paths();

    $discoverer->collect()
        ->each(function (array $asset) use ($migrations): void {
            expect($migrations)->toContain($asset['path']);
        });

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
