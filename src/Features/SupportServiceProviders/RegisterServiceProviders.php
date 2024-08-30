<?php

namespace Mozex\Modules\Features\SupportServiceProviders;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class RegisterServiceProviders extends Feature
{
    public function register(): void
    {
        if (AssetType::ServiceProviders->isDeactive()) {
            return;
        }

        AssetType::ServiceProviders->scout()->collect()
            ->each(function (array $asset): void {
                $this->app->register($asset['namespace']);
            });
    }
}
