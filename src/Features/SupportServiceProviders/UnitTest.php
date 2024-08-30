<?php

use Modules\First\Providers\UserServiceProvider;
use Modules\First\Providers\WrongServiceProvider;
use Modules\Second\Providers\TeamServiceProvider;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportServiceProviders\ServiceProvidersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::ServiceProviders->value.'.active',
        false
    );

    $discoverer = ServiceProvidersScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = ServiceProvidersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(UserServiceProvider::class)
        ->toContain(TeamServiceProvider::class)
        ->not->toContain(WrongServiceProvider::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register service providers', function (bool $cache) {
    $discoverer = ServiceProvidersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $serviceProviders = app()->getLoadedProviders();

    $discoverer->collect()
        ->each(function (array $asset) use ($serviceProviders) {
            expect($serviceProviders)->toHaveKey($asset['namespace']);
        });

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
