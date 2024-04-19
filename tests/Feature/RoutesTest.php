<?php

use Illuminate\Support\Facades\Route;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\RoutesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Routes->value.'.active',
        false
    );

    $discoverer = RoutesScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = RoutesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Routes/web.php')))
        ->toContain(realpath(Modules::modulesPath('First/Routes/api.php')))
        ->toContain(realpath(Modules::modulesPath('First/Routes/console.php')))
        ->toContain(realpath(Modules::modulesPath('First/Routes/channels.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/web.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/undefined.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/custom.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/console.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/channels.php')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can load routes', function (bool $cache) {
    $discoverer = RoutesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    expect(Route::getRoutes()->getByName('web-first'))->not->toBeNull()
        ->getPrefix()->toBeEmpty()
        ->gatherMiddleware()->toHaveCount(1)->toContain('web')
        ->and(route('web-first'))->toBe('http://localhost/web-first')
        ->and(Route::getRoutes()->getByName('api-first'))->not->toBeNull()
        ->getPrefix()->toBe('api')
        ->gatherMiddleware()->toHaveCount(1)->toContain('api')
        ->and(route('api-first'))->toBe('http://localhost/api/api-first')
        ->and(Route::getRoutes()->getByName('web-second'))->not->toBeNull()
        ->getPrefix()->toBeEmpty()
        ->gatherMiddleware()->toHaveCount(1)->toContain('web')
        ->and(route('web-second'))->toBe('http://localhost/web-second')
        ->and(Route::getRoutes()->getByName('undefined-second'))->not->toBeNull()
        ->getPrefix()->toBeEmpty()
        ->gatherMiddleware()->toBeEmpty()
        ->and(route('undefined-second'))->toBe('http://localhost/undefined-second')
        ->and(Route::getRoutes()->getByName('custom::custom-second'))->not->toBeNull()
        ->getPrefix()->toBe('custom')
        ->gatherMiddleware()->toHaveCount(2)->toContain('web', 'api')
        ->and(route('custom::custom-second'))->toBe('http://localhost/custom/custom-second');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
