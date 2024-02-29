<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\HelpersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Helpers->value.'.active',
        false
    );

    $discoverer = HelpersScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = HelpersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Helpers/Shared.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Helpers/testing.php')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register helpers', function (bool $cache) {
    $discoverer = HelpersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(firstHelper())
        ->toBe('First Helper')
        ->and(secondHelper())
        ->toBe('Second Helper');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
