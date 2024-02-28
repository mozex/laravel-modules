<?php

use Modules\First\Database\Seeders\FirstDatabaseSeeder;
use Modules\First\Database\Seeders\UserSeeder;
use Modules\Second\Database\Seeders\SecondDatabaseSeeder;
use Modules\Second\Database\Seeders\TeamDatabaseSeeder;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\SeedersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Seeders->value.'.active',
        false
    );

    $discoverer = SeedersScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = SeedersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(SecondDatabaseSeeder::class)
        ->toContain(FirstDatabaseSeeder::class)
        ->not->toContain(UserSeeder::class)
        ->not->toContain(TeamDatabaseSeeder::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can return seeders', function (bool $cache) {
    $discoverer = SeedersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Modules::seeders())
        ->toHaveCount(2)
        ->toContain(FirstDatabaseSeeder::class)
        ->toContain(SecondDatabaseSeeder::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('will not return seeders when disabled', function (bool $cache) {
    config()->set(
        'modules.'.AssetType::Seeders->value.'.active',
        false
    );

    $discoverer = SeedersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Modules::seeders())->toBeEmpty();

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
