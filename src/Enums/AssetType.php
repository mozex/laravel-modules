<?php

namespace Mozex\Modules\Enums;

use Illuminate\Support\Collection;
use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Features\SupportBladeComponents\BladeComponentsScout;
use Mozex\Modules\Features\SupportCommands\CommandsScout;
use Mozex\Modules\Features\SupportConfigs\ConfigsScout;
use Mozex\Modules\Features\SupportFilament\FilamentClustersScout;
use Mozex\Modules\Features\SupportFilament\FilamentPagesScout;
use Mozex\Modules\Features\SupportFilament\FilamentResourcesScout;
use Mozex\Modules\Features\SupportFilament\FilamentWidgetsScout;
use Mozex\Modules\Features\SupportHelpers\HelpersScout;
use Mozex\Modules\Features\SupportListeners\ListenersScout;
use Mozex\Modules\Features\SupportLivewire\LivewireComponentsScout;
use Mozex\Modules\Features\SupportMigrations\MigrationsScout;
use Mozex\Modules\Features\SupportNova\NovaResourcesScout;
use Mozex\Modules\Features\SupportRoutes\RoutesScout;
use Mozex\Modules\Features\SupportSeeders\SeedersScout;
use Mozex\Modules\Features\SupportServiceProviders\ServiceProvidersScout;
use Mozex\Modules\Features\SupportTranslations\TranslationsScout;
use Mozex\Modules\Features\SupportViews\ViewsScout;

enum AssetType: string
{
    case Commands = 'commands';
    case Migrations = 'migrations';
    case Helpers = 'helpers';
    case ServiceProviders = 'service-providers';
    case Seeders = 'seeders';
    case Translations = 'translations';
    case Configs = 'configs';
    case Views = 'views';
    case BladeComponents = 'blade-components';
    case Routes = 'routes';
    case LivewireComponents = 'livewire-components';
    case NovaResources = 'nova-resources';
    case Factories = 'factories';
    case Policies = 'policies';
    case Models = 'models';
    case Listeners = 'listeners';
    case FilamentResources = 'filament-resources';
    case FilamentPages = 'filament-pages';
    case FilamentWidgets = 'filament-widgets';
    case FilamentClusters = 'filament-clusters';

    public function scout(): ?BaseScout
    {
        return match ($this) {
            self::Commands => CommandsScout::instance(),
            self::Migrations => MigrationsScout::instance(),
            self::Helpers => HelpersScout::instance(),
            self::ServiceProviders => ServiceProvidersScout::instance(),
            self::Seeders => SeedersScout::instance(),
            self::Translations => TranslationsScout::instance(),
            self::Configs => ConfigsScout::instance(),
            self::Views => ViewsScout::instance(),
            self::BladeComponents => BladeComponentsScout::instance(),
            self::Routes => RoutesScout::instance(),
            self::LivewireComponents => LivewireComponentsScout::instance(),
            self::NovaResources => NovaResourcesScout::instance(),
            self::Listeners => ListenersScout::instance(),
            self::FilamentResources => FilamentResourcesScout::instance(),
            self::FilamentPages => FilamentPagesScout::instance(),
            self::FilamentWidgets => FilamentWidgetsScout::instance(),
            self::FilamentClusters => FilamentClustersScout::instance(),
            default => null,
        };
    }

    /**
     * @return array{active?: bool, patterns?: array<array-key, string>, namespace?: string, priority?: bool, commands_filenames?: array<array-key, string>, channels_filenames?: array<array-key, string>}
     */
    public function config(): array
    {
        return config('modules')[$this->value] ?? [];
    }

    public function isActive(): bool
    {
        return $this->config()['active'] ?? false;
    }

    public function isDeactive(): bool
    {
        return ! $this->isActive();
    }

    /**
     * @return ?array<array-key, string>
     */
    public function patterns(): ?array
    {
        return $this->config()['patterns'] ?? null;
    }

    public function title(): string
    {
        return str($this->value)
            ->replace('-', ' ')
            ->title();
    }

    /**
     * @return Collection<int, BaseScout>
     */
    public static function activeScouts(): Collection
    {
        return collect(self::cases())
            ->filter(fn (self $type): bool => $type->isActive())
            ->map(fn (self $type): ?BaseScout => $type->scout())
            ->filter();
    }
}
