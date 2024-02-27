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
use Mozex\Modules\Scouts\ListenersScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Listeners->value.'.active',
        false
    );

    expect(ListenersScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(ListenersScout::create()->get())
        ->each->toHaveKeys(['module', 'path'])
        ->not->toHaveKey('namespace');
});

test('scout will select correct paths', function () {
    expect(ListenersScout::create()->collect()->pluck('path'))
        ->toContain(realpath(Modules::modulesPath('First/Listeners')))
        ->toContain(realpath(Modules::modulesPath('Second/Listeners')));
});

it('can attach listeners to events', function () {
    Event::fake();

    Event::assertListening(TestEvent::class, TestListener::class);
    Event::assertListening(TestEvent::class, UserCreatedListener::class);
    Event::assertListening(UserDeletedEvent::class, NotifyDeletedUserListener::class);
    Event::assertListening(TeamCreatedEvent::class, NotifyTeamOwnerListener::class);
});
