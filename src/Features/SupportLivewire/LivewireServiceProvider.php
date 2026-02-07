<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Livewire\Livewire;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class LivewireServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::LivewireComponents;
    }

    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Livewire::class);
    }

    public function boot(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                Livewire::component(
                    $this->getViewName($asset, static::asset()),
                    $asset['namespace']
                );
            });
    }
}
