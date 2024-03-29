<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\ConfigsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Configs->value.'.active',
        false
    );

    $discoverer = ConfigsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = ConfigsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Config/first.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Config/test.php')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can load configs', function (bool $cache) {
    $discoverer = ConfigsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(config('first.config'))->toBe('first config')
        ->and(config('test.config'))->toBe('overridden test config')
        ->and(config('test.second-config'))->toBe('second config');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
