<?php

namespace Mozex\Modules;

use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ModulesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-modules')
            ->hasConfigFile()
            ->hasCommand(CacheCommand::class)
            ->hasCommand(ClearCommand::class);
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
        $this->registerFeatures();
    }

    protected function registerFeatures(): void
    {
        foreach ($this->getFeatures() as $feature) {
            $this->app->register($feature);
        }
    }
}
