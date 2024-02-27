<?php

use Illuminate\Support\Facades\Blade;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Scouts\ViewsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Views->value.'.active',
        false
    );

    expect(ViewsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(ViewsScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct paths', function () {
    expect(ViewsScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Resources/views')))
        ->toContain(realpath(Modules::modulesPath('Second/Resources/views')));
});

it('can load views', function () {
    $views = app('view')->getFinder()->getHints();

    ViewsScout::create()->collect()
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
});
