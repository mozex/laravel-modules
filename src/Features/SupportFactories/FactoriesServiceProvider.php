<?php

namespace Mozex\Modules\Features\SupportFactories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use ReflectionProperty;

class FactoriesServiceProvider extends Feature
{
    public function boot(): void
    {
        if (AssetType::Factories->isDeactive()) {
            return;
        }

        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return sprintf(
                    '%s%s\\%s%sFactory',
                    config('modules.modules_namespace'),
                    $module,
                    AssetType::Factories->config()['namespace'],
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
