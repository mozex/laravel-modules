<?php

namespace Mozex\Modules\Features\SupportModels;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use ReflectionProperty;

class ModelsServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Models->isDeactive()) {
            return;
        }

        Factory::guessModelNamesUsing(function (Factory $factory) {
            if ($module = Modules::moduleNameFromNamespace($factory::class)) {
                return sprintf(
                    '%s%s\\%s%s',
                    config('modules.modules_namespace'),
                    $module,
                    AssetType::Models->config()['namespace'],
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
                if (property_exists(Factory::class, 'modelNameResolver')) {
                    $property = (new ReflectionProperty(Factory::class, 'modelNameResolver'));

                    $value = null;
                } else {
                    $property = new ReflectionProperty(Factory::class, 'modelNameResolvers');

                    $value = $property->getValue();

                    unset($value[Factory::class]);
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
