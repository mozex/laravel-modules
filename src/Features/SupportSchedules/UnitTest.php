<?php

use Illuminate\Console\Scheduling\Schedule;
use Modules\First\Console\Kernel as FirstKernel;
use Modules\Second\Console\Kernel as SecondKernel;
use Modules\Second\Console\WrongKernel;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportSchedules\SchedulesScout;
use Mozex\Modules\Tests\Kernel;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Schedules->value.'.active',
        false
    );

    $discoverer = SchedulesScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache) {
    $discoverer = SchedulesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(FirstKernel::class)
        ->toContain(SecondKernel::class)
        ->not->toContain(WrongKernel::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register schedules', function (bool $cache) {
    $discoverer = SchedulesScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $schedules = collect(app(Schedule::class)->events())
        ->pluck('command')
        ->flatten()
        ->map(
            fn ($command) => str_contains($command, 'artisan')
                ? str($command)->explode(' ')->last()
                : $command
        )
        ->toArray();

    expect($schedules)
        ->toContain('first-scheduled-command-1')
        ->toContain('second-scheduled-command-1')
        ->toContain('second-scheduled-command-2');

    if (method_exists(Kernel::class, 'addCommandRoutePaths')) {
        expect($schedules)
            ->toContain('first:console-command-1')
            ->toContain('first-scheduled-command-2')
            ->toContain('first-scheduled-command-3')
            ->toContain('second:console-command-1')
            ->toContain('second-scheduled-command-3');
    }

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
