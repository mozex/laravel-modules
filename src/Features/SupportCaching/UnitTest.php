<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;

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
