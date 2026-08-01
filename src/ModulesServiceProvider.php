<?php

namespace Mozex\Modules;

use Mozex\Modules\Features\SupportCaching\CacheCommand;
use Mozex\Modules\Features\SupportCaching\ClearCommand;
use Mozex\Modules\Features\SupportCaching\ListCommand;
use Mozex\Modules\Features\SupportCaching\RuntimeCache;
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
            ->hasCommand(ClearCommand::class)
            ->hasCommand(ListCommand::class);
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
