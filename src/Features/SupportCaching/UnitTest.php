<?php

use Illuminate\Support\Facades\Artisan;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;
use Pest\Expectation;

it('can cache', function () {
    $scouts = AssetType::activeScouts();

    Artisan::call(ClearCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeFalse());

    Artisan::call(CacheCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeTrue());

    Artisan::call(ClearCommand::class);
});

it('can clear', function () {
    $scouts = AssetType::activeScouts();

    Artisan::call(CacheCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeTrue());

    Artisan::call(ClearCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeFalse());
});
