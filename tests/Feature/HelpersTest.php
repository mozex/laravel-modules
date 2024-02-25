<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\HelpersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Helpers->value.'.active',
        false
    );

    expect(HelpersScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(HelpersScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct files', function () {
    expect(HelpersScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Helpers/Shared.php')))
        ->toContain(realpath(Modules::modulesPath('Second/Helpers/testing.php')));
});

it('can register helpers', function () {
    expect(firstHelper())
        ->toBe('First Helper')
        ->and(secondHelper())
        ->toBe('Second Helper');
});
