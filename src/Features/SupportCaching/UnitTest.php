<?php

use Illuminate\Support\Facades\Artisan;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;
use Mozex\Modules\Features\SupportCaching\ListCommand;
use Pest\Expectation;

it('can cache', function () {
    $scouts = AssetType::activeScouts();

    Artisan::call(ClearCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeFalse());

    Artisan::call(CacheCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeTrue());

    Artisan::call(ClearCommand::class);
});

it('can clear', function () {
    $scouts = AssetType::activeScouts();

    Artisan::call(CacheCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeTrue());

    Artisan::call(ClearCommand::class);

    expect($scouts)
        ->each(fn (Expectation $scout) => $scout->isCached()->toBeFalse());
});

it('can list modules', function (): void {
    Artisan::call(ListCommand::class);
    $output = Artisan::output();

    expect($output)
        ->toContain('First')
        ->toContain('Second')
        ->toContain('PWA')
        ->toContain('Enabled');
});

it('shows modules in correct order', function (): void {
    Artisan::call(ListCommand::class);
    $output = Artisan::output();

    $secondPosition = strpos($output, 'Second');
    $firstPosition = strpos($output, 'First');

    expect($secondPosition)->toBeLessThan($firstPosition);
});

it('shows disabled modules', function (): void {
    config()->set('modules.modules.First.active', false);

    Artisan::call(ListCommand::class);
    $output = Artisan::output();

    expect($output)->toContain('Disabled');
});

it('shows warning when no modules found', function (): void {
    config()->set('modules.modules_directory', 'NonExistent');

    Artisan::call(ListCommand::class);
    $output = Artisan::output();

    expect($output)->toContain('No modules found');
});

it('shows asset counts per module', function (): void {
    Artisan::call(ListCommand::class);
    $output = Artisan::output();

    expect($output)
        ->toContain('Commands')
        ->toContain('Views')
        ->toContain('Routes');
});

it('counts files inside directory-based asset scouts', function (): void {
    Artisan::call(ListCommand::class);
    $output = preg_replace('/\e\[[0-9;]*m/', '', (string) Artisan::output());

    $secondStart = strpos((string) $output, 'Second');
    $pwaStart = strpos((string) $output, 'PWA');
    $secondSection = substr((string) $output, (int) $secondStart, (int) $pwaStart - (int) $secondStart);

    expect($secondSection)->toMatch('/\|\s*Migrations\s*\|\s*2\s*\|/');
});
