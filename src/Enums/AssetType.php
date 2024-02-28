<?php

namespace Mozex\Modules\Enums;

use Illuminate\Support\Collection;
use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Scouts\BladeComponentsScout;
use Mozex\Modules\Scouts\CommandsScout;
use Mozex\Modules\Scouts\ConfigsScout;
use Mozex\Modules\Scouts\HelpersScout;
use Mozex\Modules\Scouts\ListenersScout;
use Mozex\Modules\Scouts\LivewireComponentsScout;
use Mozex\Modules\Scouts\MigrationsScout;
use Mozex\Modules\Scouts\NovaResourcesScout;
use Mozex\Modules\Scouts\RoutesScout;
use Mozex\Modules\Scouts\SchedulesScout;
use Mozex\Modules\Scouts\SeedersScout;
use Mozex\Modules\Scouts\ServiceProvidersScout;
use Mozex\Modules\Scouts\TranslationsScout;
use Mozex\Modules\Scouts\ViewsScout;

enum AssetType: string
{
    case Commands = 'commands';
    case Migrations = 'migrations';
    case Helpers = 'helpers';
    case ServiceProviders = 'service-providers';
    case Seeders = 'seeders';
    case Translations = 'translations';
    case Schedules = 'schedules';
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

    public function scout(): ?BaseScout
    {
        return match ($this) {
            self::Commands => CommandsScout::create(),
            self::Migrations => MigrationsScout::create(),
            self::Helpers => HelpersScout::create(),
            self::ServiceProviders => ServiceProvidersScout::create(),
            self::Seeders => SeedersScout::create(),
            self::Translations => TranslationsScout::create(),
            self::Schedules => SchedulesScout::create(),
            self::Configs => ConfigsScout::create(),
            self::Views => ViewsScout::create(),
            self::BladeComponents => BladeComponentsScout::create(),
            self::Routes => RoutesScout::create(),
            self::LivewireComponents => LivewireComponentsScout::create(),
            self::NovaResources => NovaResourcesScout::create(),
            self::Listeners => ListenersScout::create(),
            default => null,
        };
    }

    /**
     * @return array{active?: bool, patterns?: array<array-key, string>, namespace?: string, groups?: array, priority?: bool}
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
            ->filter(fn (self $type) => $type->isActive())
            ->map(fn (self $type) => $type->scout())
            ->filter();
    }
}
