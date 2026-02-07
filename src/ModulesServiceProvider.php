<?php

namespace Mozex\Modules;

use Mozex\Modules\Enums\AssetType;
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
     * @return array<int, array{class-string, ?AssetType}>
     */
    protected function getFeatures(): array
    {
        return [
            [Features\SupportConfigs\ConfigsServiceProvider::class, AssetType::Configs],
            [Features\SupportServiceProviders\RegisterServiceProviders::class, AssetType::ServiceProviders],
            [Features\SupportHelpers\HelpersServiceProvider::class, AssetType::Helpers],
            [Features\SupportCommands\CommandsServiceProvider::class, AssetType::Commands],
            [Features\SupportMigrations\MigrationsServiceProvider::class, AssetType::Migrations],
            [Features\SupportTranslations\TranslationsServiceProvider::class, AssetType::Translations],
            [Features\SupportViews\ViewsServiceProvider::class, AssetType::Views],
            [Features\SupportBladeComponents\BladeComponentsServiceProvider::class, AssetType::BladeComponents],
            [Features\SupportModels\ModelsServiceProvider::class, AssetType::Models],
            [Features\SupportFactories\FactoriesServiceProvider::class, AssetType::Factories],
            [Features\SupportPolicies\PoliciesServiceProvider::class, AssetType::Policies],
            [Features\SupportRoutes\RoutesServiceProvider::class, AssetType::Routes],
            [Features\SupportSchedules\SchedulesServiceProvider::class, AssetType::Schedules],
            [Features\SupportListeners\ListenersServiceProvider::class, AssetType::Listeners],
            [Features\SupportEvents\EventsServiceProvider::class, AssetType::Listeners],
            [Features\SupportLivewire\LivewireServiceProvider::class, AssetType::LivewireComponents],
            [Features\SupportFilament\FilamentServiceProvider::class, null],
            [Features\SupportNova\NovaServiceProvider::class, AssetType::NovaResources],
        ];
    }

    public function packageRegistered(): void
    {
        $this->registerFeatures();
    }

    protected function registerFeatures(): void
    {
        foreach ($this->getFeatures() as [$feature, $assetType]) {
            if ($assetType !== null && $assetType->isDeactive()) {
                continue;
            }

            $this->app->register($feature);
        }
    }
}
