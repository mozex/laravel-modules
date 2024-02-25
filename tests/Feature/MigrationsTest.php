<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\MigrationsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Migrations->value.'.active',
        false
    );

    expect(MigrationsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(MigrationsScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct paths', function () {
    expect(MigrationsScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Database/Migrations')))
        ->toContain(realpath(Modules::modulesPath('Second/Database/Migrations')));
});

it('can load migrations', function () {
    $migrations = app('migrator')->paths();

    MigrationsScout::create()->collect()
        ->each(function (array $asset) use ($migrations) {
            expect($migrations)->toContain($asset['path']);
        });
});
