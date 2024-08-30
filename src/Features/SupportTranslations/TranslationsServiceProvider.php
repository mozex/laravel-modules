<?php

namespace Mozex\Modules\Features\SupportTranslations;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;

class TranslationsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Translations->isDeactive()) {
            return;
        }

        AssetType::Translations->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadTranslationsFrom(
                    path: $asset['path'],
                    namespace: $this->getName($asset['module'])
                );

                $this->loadJsonTranslationsFrom($asset['path']);
            });
    }
}
