<?php

use Modules\First\Providers\UserServiceProvider;
use Modules\First\Providers\WrongServiceProvider;
use Modules\Second\Providers\TeamServiceProvider;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\ServiceProvidersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::ServiceProviders->value.'.active',
        false
    );

    expect(ServiceProvidersScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(ServiceProvidersScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(ServiceProvidersScout::create()->collect()->pluck('namespace'))
        ->toContain(UserServiceProvider::class)
        ->toContain(TeamServiceProvider::class)
        ->not->toContain(WrongServiceProvider::class);
});

it('can register service providers', function () {
    $serviceProviders = app()->getLoadedProviders();

    ServiceProvidersScout::create()->collect()
        ->each(function (array $asset) use ($serviceProviders) {
            expect($serviceProviders)->toHaveKey($asset['namespace']);
        });
});
