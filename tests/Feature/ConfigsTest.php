<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\ConfigsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Configs->value.'.active',
        false
    );

    expect(ConfigsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(ConfigsScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct files', function () {
    expect(ConfigsScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Config/first.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Config/test.php')));
});

it('can load configs', function () {
    expect(config('first.config'))->toBe('first config')
        ->and(config('test.config'))->toBe('overridden test config')
        ->and(config('test.second-config'))->toBe('second config');
});
