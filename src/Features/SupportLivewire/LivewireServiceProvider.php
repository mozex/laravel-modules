<?php

namespace Mozex\Modules\Features\SupportLivewire;

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
