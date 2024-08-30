<?php

use Illuminate\Support\Facades\Blade;
use Modules\First\View\Components\Filter;
use Modules\First\View\Components\WrongComponent;
use Modules\Second\View\Components\Button\Loading;
use Modules\Second\View\Components\Search;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportBladeComponents\BladeComponentsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::BladeComponents->value.'.active',
        false
    );

    $discoverer = BladeComponentsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = BladeComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(Filter::class)
        ->toContain(Search::class)
        ->toContain(Loading::class)
        ->not->toContain(WrongComponent::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can load blade components', function (bool $cache) {
    $discoverer = BladeComponentsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $components = Blade::getClassComponentAliases();

    $discoverer->collect()
        ->each(function (array $asset) use ($components) {
            expect($components)->toContain($asset['namespace']);
        });

    expect(Blade::render(
        string: '<x-first::filter name="Filter"/>',
        deleteCachedView: true
    ))
        ->toContain('Filter Component')
        ->and(Blade::render(
            string: '<x-first::select name="Select"/>',
            deleteCachedView: true
        ))
        ->toContain('Select Component')
        ->and(Blade::render(
            string: '<x-first::without-view name="Without View"/>',
            deleteCachedView: true
        ))
        ->toContain('Without View Component')
        ->and(Blade::render(
            string: '<x-second::search name="Search"/>',
            deleteCachedView: true
        ))
        ->toContain('Search Component')
        ->and(Blade::render(
            string: '<x-second::button.loading name="Loading"/>',
            deleteCachedView: true
        ))
        ->toContain('Loading Component');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
