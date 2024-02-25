<?php

namespace Mozex\Modules;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Auth\Access\Gate as GateInstance;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Laravel\Nova\Nova;
use Livewire\Livewire;
use Mozex\Modules\Commands\CacheCommand;
use Mozex\Modules\Commands\ClearCommand;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use ReflectionMethod;
use ReflectionProperty;
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

    public function packageBooted(): void
    {
        $this->bootCommands();
        $this->bootMigrations();
        $this->bootTranslations();
        $this->bootConfigs();
        $this->bootViews();
        $this->bootModels();
        $this->bootFactories();
        $this->bootPolicies();
        $this->bootRoutes();
        $this->bootSchedules();
        $this->bootLivewire();
        $this->bootNova();
    }

    public function packageRegistered(): void
    {
        $this->registerHelpers();
        $this->registerServicePorviders();
    }

    protected function bootCommands(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (AssetType::Commands->isDeactive()) {
            return;
        }

        $this->commands(
            AssetType::Commands->scout()->collect()
                ->pluck('namespace')
                ->toArray()
        );
    }

    protected function bootMigrations(): void
    {
        if (AssetType::Migrations->isDeactive()) {
            return;
        }

        $this->loadMigrationsFrom(
            AssetType::Migrations->scout()->collect()
                ->pluck('path')
                ->toArray()
        );
    }

    protected function bootTranslations(): void
    {
        if (AssetType::Translations->isDeactive()) {
            return;
        }

        AssetType::Translations->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadTranslationsFrom($asset['path'], $asset['module']);
                $this->loadJsonTranslationsFrom($asset['path']);
            });
    }

    protected function bootConfigs(): void
    {
        if (AssetType::Configs->isDeactive()) {
            return;
        }

        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            AssetType::Configs->scout()->collect()
                ->each(function (array $asset) use ($config): void {
                    $key = File::name($asset['path']);

                    $config->set(
                        key: $key,
                        value: AssetType::Configs->config()['priority']
                            ? array_merge(
                                $config->get($key, []),
                                require $asset['path']
                            )
                            : array_merge(
                                require $asset['path'],
                                $config->get($key, [])
                            )
                    );
                });
        }
    }

    protected function bootViews(): void
    {
        if (AssetType::Views->isDeactive()) {
            return;
        }

        AssetType::Views->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadViewsFrom($asset['path'], strtolower($asset['module']));
            });
    }

    protected function bootModels(): void
    {
        if (AssetType::Models->isDeactive()) {
            return;
        }

        Factory::guessModelNamesUsing(function (Factory $factory) {
            if ($module = Modules::moduleNameFromNamespace(get_class($factory))) {
                return sprintf(
                    '%s%s\\%s%s',
                    config('modules.modules_namespace'),
                    $module,
                    AssetType::Models->config()['namespace'],
                    str(get_class($factory))->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Factories->config()['namespace']
                        )
                    )->replaceLast('Factory', '')
                );
            }

            try {
                (new ReflectionProperty(Factory::class, 'modelNameResolver'))
                    ->setValue(null);

                return $factory->modelName();
            } finally {
                $this->bootModels();
            }
        });
    }

    protected function bootFactories(): void
    {
        if (AssetType::Factories->isDeactive()) {
            return;
        }

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return sprintf(
                    '%s%s\\%s%sFactory',
                    config('modules.modules_namespace'),
                    $module,
                    AssetType::Factories->config()['namespace'],
                    str($modelName)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Models->config()['namespace']
                        )
                    )
                );
            }

            try {
                (new ReflectionProperty(Factory::class, 'factoryNameResolver'))
                    ->setValue(null);

                return Factory::resolveFactoryName($modelName);
            } finally {
                $this->bootFactories();
            }
        });
    }

    protected function bootPolicies(): void
    {
        if (AssetType::Policies->isDeactive()) {
            return;
        }

        Gate::guessPolicyNamesUsing(function (string $modelName) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return sprintf(
                    '%s%s\\%s%sPolicy',
                    config('modules.modules_namespace'),
                    $module,
                    AssetType::Policies->config()['namespace'],
                    str($modelName)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Models->config()['namespace']
                        )
                    )
                );
            }

            try {
                $gate = $this->app->make(GateInstance::class);

                (new ReflectionProperty($gate, 'guessPolicyNamesUsingCallback'))
                    ->setValue($gate, null);

                $reflection = (new ReflectionMethod($gate, 'guessPolicyName'));

                return $reflection->invoke($gate, $modelName);
            } finally {
                $this->bootPolicies();
            }
        });
    }

    protected function bootRoutes(): void
    {
        if (AssetType::Routes->isDeactive()) {
            return;
        }

        AssetType::Routes->scout()->collect()
            ->each(function (array $asset): void {
                $group = File::name($asset['path']);

                Route::middleware(AssetType::Routes->config()['groups'][$group]['middlewares'] ?? [])
                    ->prefix(AssetType::Routes->config()['groups'][$group]['prefix'] ?? '')
                    ->group($asset['path']);
            });
    }

    protected function bootSchedules(): void
    {
        if (AssetType::Schedules->isDeactive()) {
            return;
        }

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            AssetType::Schedules->scout()->collect()
                ->each(function (array $asset) use ($schedule) {
                    (new $asset['namespace'])->schedule($schedule);
                });
        });
    }

    protected function bootLivewire(): void
    {
        if (! class_exists(Livewire::class)) {
            return;
        }

        if (AssetType::LivewireComponents->isDeactive()) {
            return;
        }

        AssetType::LivewireComponents->scout()->collect()
            ->each(function (array $asset): void {
                Livewire::component(
                    str(class_basename($asset['namespace']))->kebab()->toString(),
                    $asset['namespace']
                );
            });
    }

    protected function bootNova(): void
    {
        if (! class_exists(Nova::class)) {
            return;
        }

        if (AssetType::NovaResources->isDeactive()) {
            return;
        }

        Nova::serving(function (): void {
            Nova::resources(
                AssetType::NovaResources->scout()->collect()
                    ->pluck('namespace')
                    ->toArray()
            );
        });
    }

    protected function registerHelpers(): void
    {
        if (AssetType::Helpers->isDeactive()) {
            return;
        }

        AssetType::Helpers->scout()->collect()
            ->each(function (array $asset): void {
                require_once $asset['path'];
            });
    }

    protected function registerServicePorviders(): void
    {
        if (AssetType::ServiceProviders->isDeactive()) {
            return;
        }

        AssetType::ServiceProviders->scout()->collect()
            ->each(function (array $asset) {
                $this->app->register($asset['namespace']);
            });
    }
}
