<?php

namespace Mozex\Modules\Features\SupportLivewire;

use Livewire\Livewire;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class LivewireServiceProvider extends Feature
{
    public function boot(): void
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
}
