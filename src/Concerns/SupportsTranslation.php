<?php

namespace Mozex\Modules\Concerns;

use Mozex\Modules\Facades\Modules;

trait SupportsTranslation
{
    public function bootTranslations(): void
    {
        Modules::getModulesAssets(config('modules.translation_patterns'))
            ->each(function (array $asset): void {
                $this->loadTranslationsFrom($asset['path'], $asset['module']);
                $this->loadJsonTranslationsFrom($asset['path']);
            });
    }
}
