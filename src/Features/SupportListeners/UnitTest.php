<?php

use App\Events\TestEvent;
use App\Listeners\TestListener;
use Illuminate\Support\Facades\Event;
use Modules\First\Events\UserDeletedEvent;
use Modules\First\Listeners\UserCreatedListener;
use Modules\Second\Events\TeamCreatedEvent;
use Modules\Second\Listeners\NotifyDeletedUserListener;
use Modules\Second\Listeners\NotifyTeamOwnerListener;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\SupportListeners\ListenersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Listeners->value.'.active',
        false
    );

    $discoverer = ListenersScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = ListenersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Listeners')))
        ->toContain(realpath(Modules::modulesPath('Second/Listeners')));

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can attach listeners to events', function (bool $cache) {
    $discoverer = ListenersScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    Event::fake();

    Event::assertListening(TestEvent::class, TestListener::class);
    Event::assertListening(TestEvent::class, UserCreatedListener::class);
    Event::assertListening(UserDeletedEvent::class, NotifyDeletedUserListener::class);
    Event::assertListening(TeamCreatedEvent::class, NotifyTeamOwnerListener::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
