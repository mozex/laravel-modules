<?php

namespace Mozex\Modules;

use Illuminate\Support\Facades\Process;
use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;
use Mozex\Modules\Features\SupportCaching\ListCommand;
use Mozex\Modules\Features\SupportCaching\RuntimeCache;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Throwable;

class ModulesServiceProvider extends PackageServiceProvider
{
    protected string $repository = 'https://github.com/mozex/laravel-modules';

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-modules')
            ->hasConfigFile()
            ->hasCommand(CacheCommand::class)
            ->hasCommand(ClearCommand::class)
            ->hasCommand(ListCommand::class)
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->publishConfigFile()
                    ->endWith(fn (InstallCommand $command) => $this->askToStar($command));
            });
    }

    /**
     * A person gets the question, defaulting to yes. A run nobody can answer
     * (--no-interaction, or no terminal on stdin, as with CI and AI agents)
     * takes that default without asking, so it gets a note explaining the
     * browser tab instead.
     */
    protected function askToStar(InstallCommand $command): void
    {
        if (! $this->isInteractive($command)) {
            $command->info('If laravel-modules saves you time, please consider starring it on GitHub: '.$this->repository);

            $this->openInBrowser();

            return;
        }

        if (! $command->confirm('Would you like to show some love by starring laravel-modules on GitHub?', true)) {
            return;
        }

        if ($this->openInBrowser()) {
            return;
        }

        $command->info("You'll find laravel-modules at ".$this->repository);
    }

    /**
     * Laravel's own rule for prompts (stdin must be a terminal, except under
     * unit tests, where the console output is faked), except that
     * --no-interaction always wins, which keeps that path testable.
     */
    protected function isInteractive(InstallCommand $command): bool
    {
        if ($command->option('no-interaction') === true) {
            return false;
        }

        if ($this->app->runningUnitTests()) {
            return true;
        }

        return defined('STDIN') && stream_isatty(STDIN);
    }

    /**
     * Best effort: any failure returns false. On Linux the opener runs in the
     * background, because xdg-open without a detected desktop runs the browser
     * in the foreground and would hold the command until the browser closes.
     */
    protected function openInBrowser(): bool
    {
        $command = match (PHP_OS_FAMILY) {
            'Darwin' => ['open', $this->repository],
            'Windows' => ['cmd', '/c', 'start', '', $this->repository],
            default => ['sh', '-c', 'command -v xdg-open > /dev/null && (xdg-open "$1" > /dev/null 2>&1 &)', 'sh', $this->repository],
        };

        try {
            return Process::run($command)->successful();
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @return array<int, class-string>
     */
    protected function getFeatures(): array
    {
        return [
            Features\SupportConfigs\ConfigsServiceProvider::class,
            Features\SupportServiceProviders\RegisterServiceProviders::class,
            Features\SupportHelpers\HelpersServiceProvider::class,
            Features\SupportCommands\CommandsServiceProvider::class,
            Features\SupportMigrations\MigrationsServiceProvider::class,
            Features\SupportTranslations\TranslationsServiceProvider::class,
            Features\SupportViews\ViewsServiceProvider::class,
            Features\SupportBladeComponents\BladeComponentsServiceProvider::class,
            Features\SupportModels\ModelsServiceProvider::class,
            Features\SupportFactories\FactoriesServiceProvider::class,
            Features\SupportPolicies\PoliciesServiceProvider::class,
            Features\SupportRoutes\RoutesServiceProvider::class,
            Features\SupportSchedules\SchedulesServiceProvider::class,
            Features\SupportListeners\ListenersServiceProvider::class,
            Features\SupportEvents\EventsServiceProvider::class,
            Features\SupportLivewire\LivewireServiceProvider::class,
            Features\SupportFilament\FilamentServiceProvider::class,
            Features\SupportNova\NovaServiceProvider::class,
        ];
    }

    public function packageRegistered(): void
    {
        // Pin the facade's instance into the container so injected instances
        // share state with facade calls made before this provider registered
        // (e.g. routeGroup() from an app provider).
        $this->app->instance(Modules::class, Facades\Modules::getFacadeRoot());

        $this->optimizes(
            optimize: 'modules:cache',
            clear: 'modules:clear',
            key: 'modules',
        );

        RuntimeCache::install();

        $this->registerFeatures();
    }

    protected function registerFeatures(): void
    {
        foreach ($this->getFeatures() as $feature) {
            if (! $feature::shouldRegisterFeature()) {
                continue;
            }

            $this->app->register($feature);
        }
    }
}
