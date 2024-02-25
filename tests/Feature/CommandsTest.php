<?php

use Modules\First\Console\Commands\FirstValidCommand;
use Modules\First\Console\Commands\WrongCommand;
use Modules\Second\Console\Commands\BaseCommand;
use Modules\Second\Console\Commands\ChainedCommand;
use Modules\Second\Console\Commands\SecondValidCommand;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Scouts\CommandsScout;

test('scout will not collect when disabled', function () {
    config()->set(
        'modules.'.AssetType::Commands->value.'.active',
        false
    );

    expect(CommandsScout::create()->get())->toHaveCount(0);
});

test('scout has correct structure', function () {
    expect(CommandsScout::create()->get())
        ->each->toHaveKeys(['module', 'path', 'namespace']);
});

test('scout will select correct classes', function () {
    expect(CommandsScout::create()->collect()->pluck('namespace'))
        ->toContain(FirstValidCommand::class)
        ->toContain(SecondValidCommand::class)
        ->toContain(ChainedCommand::class)
        ->not->toContain(WrongCommand::class)
        ->not->toContain(BaseCommand::class);
});

it('can register commands', function () {
    $commands = Artisan::all();

    CommandsScout::create()->collect()
        ->each(function (array $asset) use ($commands) {
            expect($commands)->toHaveKey((new $asset['namespace'])->getName());
        });
});
