<?php

namespace Mozex\Modules;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Auth\Access\Gate as GateInstance;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Events\DiscoverEvents;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Broadcast;
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
use SplFileInfo;

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
        $this->bootBladeComponents();
        $this->bootModels();
        $this->bootFactories();
        $this->bootPolicies();
        $this->bootRoutes();
        $this->bootSchedules();
        $this->bootListeners();
        $this->bootLivewire();
        $this->bootNova();
    }

    public function packageRegistered(): void
    {
        $this->registerHelpers();
        $this->registerServicePorviders();
        $this->registerFilament();
    }

    protected function bootCommands(): void
    {
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
                $this->loadTranslationsFrom(
                    path: $asset['path'],
                    namespace: $this->lowerDashedName($asset['module'])
                );
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
                $this->loadViewsFrom(
                    path: $asset['path'],
                    namespace: $this->lowerDashedName($asset['module'])
                );
            });
    }

    protected function bootBladeComponents(): void
    {
        if (AssetType::BladeComponents->isDeactive()) {
            return;
        }

        AssetType::BladeComponents->scout()->collect()
            ->each(function (array $asset): void {
                Blade::component(
                    class: $asset['namespace'],
                    alias: $this->getViewName($asset, AssetType::BladeComponents)
                );
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

        [$commands, $rest] = AssetType::Routes->scout()->collect()
            ->partition(
                fn (array $asset) => collect(AssetType::Routes->config()['commands_filenames'])
                    ->contains(File::name($asset['path']))
            );

        [$channels, $routes] = $rest
            ->partition(
                fn (array $asset) => collect(AssetType::Routes->config()['channels_filenames'])
                    ->contains(File::name($asset['path']))
            );

        $this->callAfterResolving(Kernel::class, function (Kernel $kernel) use ($commands) {
            // Compatibility with Laravel 10
            if (method_exists($kernel, 'addCommandRoutePaths')) {
                $kernel->addCommandRoutePaths(
                    $commands->pluck('path')->all()
                );
            }
        });

        $this->app->booted(function () use ($channels) {
            if ($channels->isNotEmpty()) {
                Broadcast::routes();
            }

            $channels->each(function (array $asset): void {
                require $asset['path'];
            });
        });

        if ($this->app->routesAreCached()) {
            return;
        }

        $routes->each(function (array $asset): void {
            Route::group(
                attributes: Modules::getRouteGroup(
                    name: File::name($asset['path'])
                ),
                routes: $asset['path']
            );
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

    protected function bootListeners(): void
    {
        if (AssetType::Listeners->isDeactive()) {
            return;
        }

        DiscoverEvents::guessClassNamesUsing(function (SplFileInfo $file, $basePath) {
            if (Modules::moduleNameFromPath($file->getRealPath())) {
                return str($file->getRealPath())
                    ->after(realpath(Modules::basePath()).DIRECTORY_SEPARATOR)
                    ->before('.php')
                    ->replace(DIRECTORY_SEPARATOR, '\\')
                    ->ucfirst()
                    ->toString();
            }

            try {
                $discoverEvent = $this->app->make(DiscoverEvents::class);

                (new ReflectionProperty($discoverEvent, 'guessClassNamesUsingCallback'))
                    ->setValue(null);

                $reflection = (new ReflectionMethod($discoverEvent, 'classFromFile'));

                return $reflection->invoke($discoverEvent, $file, $basePath);
            } finally {
                $this->bootListeners();
            }
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
                    $this->getViewName($asset, AssetType::LivewireComponents),
                    $asset['namespace']
                );
            });
    }

    protected function registerFilament(): void
    {
        if (! class_exists(Filament::class)) {
            return;
        }

        $this->callAfterResolving(PanelRegistry::class, function (PanelRegistry $panelRegistry) {
            collect($panelRegistry->all())
                ->each(function (Panel $panel): void {
                    if (AssetType::FilamentResources->isActive()) {
                        AssetType::FilamentResources->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverResources(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentPages->isActive()) {
                        AssetType::FilamentPages->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverPages(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentWidgets->isActive()) {
                        AssetType::FilamentWidgets->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverWidgets(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    if (AssetType::FilamentClusters->isActive()) {
                        AssetType::FilamentClusters->scout()->collect()
                            ->where('panel', strtolower($panel->getId()))
                            ->each(fn (array $asset) => $panel->discoverClusters(
                                in: $asset['path'],
                                for: $asset['namespace']
                            ));
                    }

                    collect(
                        (new ReflectionProperty($panel, 'livewireComponents'))
                            ->getValue($panel)
                    )
                        ->filter(
                            fn (string $class) => str_starts_with(
                                haystack: $class,
                                needle: config('modules.modules_namespace')
                            )
                        )
                        ->flip()
                        ->each(Livewire::component(...));
                });
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

    protected function getViewName(array $asset, AssetType $type): string
    {
        foreach ($type->patterns() as $pattern) {
            $sub = str(realpath($asset['path']))
                ->replaceFirst(realpath(Modules::modulesPath()), '')
                ->replace('\\', '/')
                ->replaceFirst('/', '')
                ->replaceMatches(
                    str($pattern)
                        ->replaceFirst('*', '.*?')
                        ->replace('/', '\/')
                        ->prepend('/')
                        ->append('\//')
                        ->toString(),
                    ''
                )
                ->before('.php')
                ->explode('/')
                ->filter();

            if ($sub->first() === $asset['module'] && $sub->count() > 1) {
                continue;
            }

            return sprintf(
                '%s::%s',
                $this->lowerDashedName($asset['module']),
                $sub->map($this->lowerDashedName(...))
                    ->implode('.')
            );
        }

        return sprintf(
            '%s::%s',
            $this->lowerDashedName($asset['module']),
            strtolower(class_basename($asset['namespace']))
        );
    }

    protected function lowerDashedName(string $name): string
    {
        $str = str($name);

        if ($name === $str->upper()->toString()) {
            return $str->lower()->toString();
        }

        return $str
            ->replaceMatches('/(?<! )[A-Z]/', '-$0')
            ->replaceFirst('-', '')
            ->lower()
            ->toString();
    }
}
