<?php

namespace Mozex\Modules\Features\SupportFactories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;
use ReflectionProperty;

class FactoriesServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Factories;
    }

    #[Override]
    public function boot(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return sprintf(
                    '%s%s\\%s%sFactory',
                    config('modules.modules_namespace'),
                    $module,
                    static::asset()->config()['namespace'],
                    str($modelName)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Models->config()['namespace']
                        )
                    )
                );
            }

            try {
                (new ReflectionProperty(Factory::class, 'factoryNameResolver'))
                    ->setValue(null, null);

                return Factory::resolveFactoryName($modelName);
            } finally {
                $this->boot();
            }
        });
    }
}
