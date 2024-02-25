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

    expect(RoutesScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(RoutesScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct files', function () {
    expect(RoutesScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Routes/web.php')))
        ->toContain(realpath(Modules::modulesPath('First/Routes/api.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/web.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/undefined.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Routes/custom.php')));
});

it('can load routes', function () {
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
        ->and(Route::getRoutes()->getByName('custom-second'))->not->toBeNull()
        ->getPrefix()->toBe('custom')
        ->gatherMiddleware()->toHaveCount(2)->toContain('web', 'api')
        ->and(route('custom-second'))->toBe('http://localhost/custom/custom-second');
});
