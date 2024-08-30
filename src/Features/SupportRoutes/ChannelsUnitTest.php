<?php

use Illuminate\Support\Facades\Broadcast;
use Mozex\Modules\Features\SupportRoutes\RoutesScout;

it('can register channels', function (bool $cache) {
    $discoverer = RoutesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $channels = collect(Broadcast::getChannels())->keys();

    expect($channels)
        ->toContain('App.Models.User.{id}')
        ->toContain('Chat.{userId}');

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
