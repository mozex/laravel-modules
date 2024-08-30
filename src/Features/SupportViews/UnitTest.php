<?php

use Illuminate\Support\Facades\Blade;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportViews\ViewsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Views->value.'.active',
        false
    );

    $discoverer = ViewsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = ViewsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Resources/views')))
        ->toContain(realpath(Modules::modulesPath('Second/Resources/views')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can load views', function (bool $cache) {
    $discoverer = ViewsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $views = app('view')->getFinder()->getHints();

    $discoverer->collect()
        ->each(function (array $asset) use ($views) {
            expect($views)->toHaveKey(strtolower($asset['module']))
                ->and($views[strtolower($asset['module'])])->toHaveCount(1)->toContain($asset['path']);
        });

    expect(view('first::first')->render())
        ->toContain('First Page')
        ->and(view('second::second')->render())
        ->toContain('Second Page')
        ->and(view('second::pages.page')->render())
        ->toContain('Nested Page')
        ->and(view('pwa::head')->render())
        ->toContain('PWA Head')
        ->and(Blade::render(
            string: '<x-pwa::manifest/>',
            deleteCachedView: true
        ))
        ->toContain('Manifest Component')
        ->and(Blade::render(
            string: '<x-first::input/>',
            deleteCachedView: true
        ))
        ->toContain('Input Component')
        ->and(Blade::render(
            string: '<x-second::checkbox/>',
            deleteCachedView: true
        ))
        ->toContain('Checkbox Component')
        ->and(Blade::render(
            string: '<x-second::button.submit/>',
            deleteCachedView: true
        ))
        ->toContain('Submit Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
