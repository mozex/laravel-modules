<?php

namespace Mozex\Modules\Features\SupportModels;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;
use ReflectionProperty;

class ModelsServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Models;
    }

    #[Override]
    public function boot(): void
    {
        Factory::guessModelNamesUsing(function (Factory $factory) {
            if ($module = Modules::moduleNameFromNamespace($factory::class)) {
                return sprintf(
                    '%s%s\\%s%s',
                    config('modules.modules_namespace'),
                    $module,
                    static::asset()->config()['namespace'],
                    str($factory::class)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Factories->config()['namespace']
                        )
                    )->replaceLast('Factory', '')
                );
            }

            try {
                if (property_exists(Factory::class, 'modelNameResolvers')) {
                    $property = new ReflectionProperty(Factory::class, 'modelNameResolvers');

                    $value = $property->getValue();

                    unset($value[Factory::class]);
                } else {
                    // Backward compatibility
                    $property = (new ReflectionProperty(Factory::class, 'modelNameResolver'));

                    $value = null;

                }

                $property
                    ->setValue($value);

                return $factory->modelName();
            } finally {
                $this->boot();
            }
        });
    }
}
