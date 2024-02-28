<?php

use Mozex\Modules\Commands\CacheCommand;
use Mozex\Modules\Commands\ClearCommand;
use Mozex\Modules\Enums\AssetType;

use function Pest\Laravel\artisan;

it('can cache', function () {
    $scouts = AssetType::activeScouts();

    artisan(ClearCommand::class)->run();

    expect($scouts)
        ->each(fn ($scout) => $scout->isCached()->toBeFalse());

    artisan(CacheCommand::class)->run();

    expect($scouts)
        ->each(fn ($scout) => $scout->isCached()->toBeTrue());

    artisan(ClearCommand::class)->run();
});

it('can clear', function () {
    $scouts = AssetType::activeScouts();

    artisan(CacheCommand::class)->run();

    expect($scouts)
        ->each(fn ($scout) => $scout->isCached()->toBeTrue());

    artisan(ClearCommand::class)->run();

    expect($scouts)
        ->each(fn ($scout) => $scout->isCached()->toBeFalse());
});
