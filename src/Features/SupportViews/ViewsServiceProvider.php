<?php

namespace Mozex\Modules\Features\SupportViews;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class ViewsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Views;
    }

    #[Override]
    public function boot(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadViewsFrom(
                    path: $asset['path'],
                    namespace: $this->getName($asset['module'])
                );
            });
    }
}
