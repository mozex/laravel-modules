<?php

namespace Mozex\Modules\Features\SupportTranslations;

use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Features\Feature;
use Override;

class TranslationsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Translations;
    }

    #[Override]
    public function boot(): void
    {
        static::asset()->scout()->collect()
            ->each(function (array $asset): void {
                $this->loadTranslationsFrom(
                    path: $asset['path'],
                    namespace: $this->getName($asset['module'])
                );

                $this->loadJsonTranslationsFrom($asset['path']);
            });
    }
}
