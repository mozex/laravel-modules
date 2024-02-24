<?php

use Mozex\Modules\Scouts\ServiceProvidersScout;

it('can register service providers', function () {
    $serviceProviders = app()->getLoadedProviders();

    ServiceProvidersScout::create()->collect()
        ->each(function (array $asset) use ($serviceProviders) {
            expect($serviceProviders)->toHaveKey($asset['namespace']);
        });
});
