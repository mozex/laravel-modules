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

    #[\Override]
    public static function shouldRegisterFeature(): bool
    {
        return parent::shouldRegisterFeature()
            && class_exists(Livewire::class);
    }

    #[\Override]
    public function boot(): void
    {
        LivewireComponentsScout::instance()->collect()
            ->each(function (array $asset): void {
                Livewire::component(
                    // Fallback for cache files built by older package versions.
                    $asset['alias'] ?? $this->getViewName($asset, static::asset()),
                    $asset['namespace']
                );
            });
    }
}
