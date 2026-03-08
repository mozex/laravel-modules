<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Livewire\Finder\Finder;
use Livewire\Livewire;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class LivewireServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::LivewireComponents;
    }

    #[Override]
    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Livewire::class);
    }

    #[Override]
    public function register(): void
    {
        // Workaround: https://github.com/livewire/livewire/pull/10076
        // Livewire's generateNameFromClass() doesn't prepend namespace prefix for
        // components registered via addNamespace(), breaking Route::livewire() with
        // class references. This override fixes the method until the upstream PR is merged.
        $this->app->extend('livewire.finder', function (Finder $finder): NamespacedFinder {
            return new NamespacedFinder($finder);
        });
    }

    #[Override]
    public function boot(): void
    {
        $config = static::asset()->config();

        static::asset()->scout()->collect()
            ->each(function (array $asset) use ($config): void {
                $viewDirectory = sprintf(
                    '%s/%s',
                    dirname($asset['path']),
                    $config['view_path']
                );

                Livewire::addNamespace(
                    namespace: $this->getName($asset['module']),
                    viewPath: $viewDirectory,
                    classNamespace: $asset['namespace'],
                    classPath: $asset['path'],
                    classViewPath: $viewDirectory,
                );
            });
    }
}
