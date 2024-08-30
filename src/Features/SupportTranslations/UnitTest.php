<?php

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportTranslations\TranslationsScout;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::Translations->value.'.active',
        false
    );

    $discoverer = TranslationsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = TranslationsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Lang')))
        ->toContain(realpath(Modules::modulesPath('Second/Lang')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can load translations', function (bool $cache) {
    $discoverer = TranslationsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $loader = app('translator')->getLoader();

    $discoverer->collect()
        ->each(function (array $asset) use ($loader) {
            expect($loader->namespaces())->toHaveKey(strtolower($asset['module']))->toContain($asset['path'])
                ->and($loader->jsonPaths())->toContain($asset['path']);
        });

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
