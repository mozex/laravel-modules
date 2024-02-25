<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\TranslationsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Translations->value.'.active',
        false
    );

    expect(TranslationsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(TranslationsScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct paths', function () {
    expect(TranslationsScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Resources/lang')))
        ->toContain(realpath(Modules::modulesPath('Second/Resources/lang')));
});

it('can load translations', function () {
    $loader = app('translator')->getLoader();

    TranslationsScout::create()->collect()
        ->each(function (array $asset) use ($loader) {
            expect($loader->namespaces())->toHaveKey($asset['module'])->toContain($asset['path'])
                ->and($loader->jsonPaths())->toContain($asset['path']);
        });
});
