<?php

use Modules\First\Nova\ActionUser;
use Modules\First\Nova\User;
use Modules\Second\Nova\Team;
use Modules\Second\Nova\WrongResource;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportNova\NovaResourcesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::NovaResources->value.'.active',
        false
    );

    $discoverer = NovaResourcesScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = NovaResourcesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(User::class)
        ->toContain(Team::class)
        ->not->toContain(ActionUser::class)
        ->not->toContain(WrongResource::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
