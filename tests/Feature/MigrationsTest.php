<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\MigrationsScout;

test('scout will not collect when disabled', function () {
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

test('discovering will work', function (bool $cache) {
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

it('can load migrations', function (bool $cache) {
    $discoverer = MigrationsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $migrations = app('migrator')->paths();

    $discoverer->collect()
        ->each(function (array $asset) use ($migrations) {
            expect($migrations)->toContain($asset['path']);
        });

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
