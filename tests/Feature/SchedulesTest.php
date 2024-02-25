<?php

use Illuminate\Console\Scheduling\Schedule;
use Modules\First\Console\Kernel as FirstKernel;
use Modules\Second\Console\Kernel as SecondKernel;
use Modules\Second\Console\WrongKernel;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\SchedulesScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Schedules->value.'.active',
        false
    );

    expect(SchedulesScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(SchedulesScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(SchedulesScout::create()->collect()->pluck('namespace'))
        ->toContain(FirstKernel::class)
        ->toContain(SecondKernel::class)
        ->not->toContain(WrongKernel::class);
});

it('can register schedules', function () {
    $schedules = collect(app(Schedule::class)->events())
        ->select('command')
        ->flatten()
        ->toArray();

    expect($schedules)
        ->toContain('first-scheduled-command-1')
        ->toContain('second-scheduled-command-1')
        ->toContain('second-scheduled-command-2');
});
