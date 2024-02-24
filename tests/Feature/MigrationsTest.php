<?php

use Mozex\Modules\Scouts\MigrationsScout;

it('can load migratons', function () {
    $migrations = app('migrator')->paths();

    MigrationsScout::create()->collect()
        ->each(function (array $asset) use ($migrations) {
            expect($migrations)->toContain($asset['path']);
        });
});
