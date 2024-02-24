<?php

use Modules\First\Database\Seeders\FirstDatabaseSeeder;
use Modules\Second\Database\Seeders\SecondDatabaseSeeder;
use Mozex\Modules\Facades\Modules;

it('can register service providers', function () {
    expect(Modules::seeders())
        ->toHaveCount(2)
        ->toContain(FirstDatabaseSeeder::class)
        ->toContain(SecondDatabaseSeeder::class);
});
