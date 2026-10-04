<?php

declare(strict_types=1);

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

const STAR_QUESTION = 'Would you like to show some love by starring laravel-modules on GitHub?';

beforeEach(function (): void {
    Process::fake();

    @unlink($this->app->configPath('modules.php'));
});

afterEach(function (): void {
    @unlink($this->app->configPath('modules.php'));
});

function assertOpenedRepository(): void
{
    Process::assertRan(
        fn (PendingProcess $process): bool => in_array('https://github.com/mozex/laravel-modules', (array) $process->command, true)
    );
}

it('publishes the config file', function (): void {
    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'no')
        ->assertSuccessful();

    expect(file_get_contents($this->app->configPath('modules.php')))
        ->toBe(file_get_contents(__DIR__.'/../config/modules.php'));
});

it('leaves an existing config file alone', function (): void {
    file_put_contents($this->app->configPath('modules.php'), '<?php return [];');

    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'no')
        ->assertSuccessful();

    expect(file_get_contents($this->app->configPath('modules.php')))->toBe('<?php return [];');
});

it('opens the repository when the user agrees to star it', function (): void {
    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'yes')
        ->doesntExpectOutputToContain('please consider starring')
        ->doesntExpectOutputToContain("You'll find")
        ->assertSuccessful();

    assertOpenedRepository();
});

it('opens nothing when the user declines', function (): void {
    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'no')
        ->assertSuccessful();

    Process::assertNothingRan();
});

it('prints the repository link when the browser cannot be opened', function (): void {
    Process::fake(['*' => Process::result(exitCode: 1)]);

    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'yes')
        ->expectsOutputToContain("You'll find laravel-modules at https://github.com/mozex/laravel-modules")
        ->assertSuccessful();
});

it('prints the repository link when opening the browser throws', function (): void {
    Process::fake(fn () => throw new RuntimeException('No opener.'));

    $this->artisan('modules:install')
        ->expectsConfirmation(STAR_QUESTION, 'yes')
        ->expectsOutputToContain("You'll find laravel-modules at https://github.com/mozex/laravel-modules")
        ->assertSuccessful();
});

it('opens the repository with a note instead of asking when nobody can answer', function (): void {
    $this->artisan('modules:install', ['--no-interaction' => true])
        ->expectsOutputToContain('If laravel-modules saves you time, please consider starring it on GitHub: https://github.com/mozex/laravel-modules')
        ->assertSuccessful();

    assertOpenedRepository();

    expect($this->app->configPath('modules.php'))->toBeFile();
});
