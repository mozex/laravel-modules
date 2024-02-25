<?php

use Modules\First\Nova\ActionUser;
use Modules\First\Nova\User;
use Modules\Second\Nova\Team;
use Modules\Second\Nova\WrongResource;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\NovaResourcesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::NovaResources->value.'.active',
        false
    );

    expect(NovaResourcesScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(NovaResourcesScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(NovaResourcesScout::create()->collect()->pluck('namespace'))
        ->toContain(User::class)
        ->toContain(Team::class)
        ->not->toContain(ActionUser::class)
        ->not->toContain(WrongResource::class);
});
