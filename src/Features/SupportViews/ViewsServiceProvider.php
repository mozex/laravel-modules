<?php

namespace Mozex\Modules\Features\SupportViews;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class ViewsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Views->isDeactive()) {
            return;
        }

        AssetType::Views->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadViewsFrom(
                    path: $asset['path'],
                    namespace: $this->getName($asset['module'])
                );
            });
    }
}
