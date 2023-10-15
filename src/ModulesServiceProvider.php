<?php

namespace Mozex\Modules;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Foundation\CachesConfiguration;
use Mozex\Modules\Concerns\SupportsCommand;
use Mozex\Modules\Concerns\SupportsConfig;
use Mozex\Modules\Concerns\SupportsFactory;
use Mozex\Modules\Concerns\SupportsHelpers;
use Mozex\Modules\Concerns\SupportsLivewire;
use Mozex\Modules\Concerns\SupportsMigration;
use Mozex\Modules\Concerns\SupportsNova;
use Mozex\Modules\Concerns\SupportsRoutes;
use Mozex\Modules\Concerns\SupportsTranslation;
use Mozex\Modules\Concerns\SupportsView;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ModulesServiceProvider extends PackageServiceProvider
{
    use SupportsCommand;
    use SupportsConfig;
    use SupportsFactory;
    use SupportsHelpers;
    use SupportsLivewire;
    use SupportsMigration;
    use SupportsNova;
    use SupportsRoutes;
    use SupportsTranslation;
    use SupportsView;

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-modules')
            ->hasConfigFile();
    }

    public function packageBooted(): void
    {
        $this->bootRoutes();
        $this->bootMigrations();
        $this->bootFactories();
        $this->bootTranslations();
        $this->bootConfigs();
        $this->bootViews();
        $this->bootCommands();
        $this->bootNova();
        $this->bootLivewire();
    }

    public function packageRegistered(): void
    {
        $this->registerHelpers();
    }

    /**
     * @throws BindingResolutionException
     */
    protected function mergeConfigWithProiorityFrom(string $path, string $key): void
    {
        if (! ($this->app instanceof CachesConfiguration && $this->app->configurationIsCached())) {
            $config = $this->app->make('config');

            $config->set($key, array_merge(
                $config->get($key, []),
                require $path
            ));
        }
    }
}
