<?php

use Modules\First\Console\Commands\ExtendedCommand;
use Modules\First\Console\Commands\FirstValidCommand;
use Modules\First\Console\Commands\WrongCommand;
use Modules\Second\Console\Commands\BaseCommand;
use Modules\Second\Console\Commands\ChainedCommand;
use Modules\Second\Console\Commands\SecondValidCommand;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportCommands\CommandsScout;
use Mozex\Modules\Tests\Kernel;

test('scout will not collect when disabled', function (): void {
    config()->set(
        'modules.'.AssetType::Commands->value.'.active',
        false
    );

    $discoverer = CommandsScout::create();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->cache();

    expect($discoverer->get())->toHaveCount(0);

    $discoverer->clear();
});

test('discovering will work', function (bool $cache): void {
    $discoverer = CommandsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $collection = $discoverer->collect();

    expect($collection)
        ->each->toHaveKeys(['module', 'path', 'namespace'])
        ->and($collection->pluck('namespace'))
        ->toContain(FirstValidCommand::class)
        ->toContain(SecondValidCommand::class)
        ->toContain(ChainedCommand::class)
        ->toContain(ExtendedCommand::class)
        ->not->toContain(WrongCommand::class)
        ->not->toContain(BaseCommand::class);

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);

it('can register commands', function (bool $cache): void {
    $discoverer = CommandsScout::create();

    if ($cache) {
        $discoverer->cache();
    }

    $commands = collect(Artisan::all())->keys();

    $discoverer->collect()
        ->each(function (array $asset) use ($commands): void {
            expect($commands)->toContain((new $asset['namespace'])->getName());
        });

    if (method_exists(Kernel::class, 'addCommandRoutePaths')) {
        expect($commands)
            ->toContain('first:console-command-1')
            ->toContain('second:console-command-1');
    }

    if ($cache) {
        $discoverer->clear();
    }
})->with([
    'without cache' => false,
    'with cache' => true,
]);
