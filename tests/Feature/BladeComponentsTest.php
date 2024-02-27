<?php

use Illuminate\Support\Facades\Blade;
use Modules\First\View\Components\Filter;
use Modules\First\View\Components\WrongComponent;
use Modules\Second\View\Components\Button\Loading;
use Modules\Second\View\Components\Search;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\BladeComponentsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::BladeComponents->value.'.active',
        false
    );

    expect(BladeComponentsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(BladeComponentsScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(BladeComponentsScout::create()->collect()->pluck('namespace'))
        ->toContain(Filter::class)
        ->toContain(Search::class)
        ->toContain(Loading::class)
        ->not->toContain(WrongComponent::class);
});

it('can load blade components', function () {
    $components = Blade::getClassComponentAliases();

    BladeComponentsScout::create()->collect()
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
});
