<?php

use Mozex\Modules\Scouts\CommandsScout;

it('can register commands', function () {
    $commands = Artisan::all();

    CommandsScout::create()->collect()
        ->each(function (array $asset) use ($commands) {
            expect($commands)->toHaveKey((new $asset['namespace'])->getName());
        });
});
