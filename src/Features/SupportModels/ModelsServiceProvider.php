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
    /** @var array<class-string, string> */
    private array $guesses = [];

    private ?ReflectionProperty $resolverProperty = null;

    public static function asset(): AssetType
    {
        return AssetType::Models;
    }

    #[Override]
    public function boot(): void
    {
        $callback = function (Factory $factory) use (&$callback) {
            if ($module = Modules::moduleNameFromNamespace($factory::class)) {
                return $this->guesses[$factory::class] ??= sprintf(
                    '%s%s\\%s%s',
                    config('modules.modules_namespace'),
                    $module,
                    static::asset()->config()['namespace'] ?? 'Models\\',
                    str($factory::class)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Factories->config()['namespace'] ?? 'Database\\Factories\\'
                        )
                    )->replaceLast('Factory', '')
                );
            }

            try {
                if (property_exists(Factory::class, 'modelNameResolvers')) {
                    $property = $this->resolverProperty ??= new ReflectionProperty(Factory::class, 'modelNameResolvers');

                    $value = $property->getValue();

                    unset($value[Factory::class]);
                } else {
                    // Laravel < 11.44 only has the singular resolver property
                    $property = $this->resolverProperty ??= new ReflectionProperty(Factory::class, 'modelNameResolver');

                    $value = null;

                }

                $property
                    ->setValue(null, $value);

                return $factory->modelName();
            } finally {
                // Re-register the same closure so per-call allocations and
                // closure-identity churn are avoided on the fallback path.
                Factory::guessModelNamesUsing($callback);
            }
        };

        Factory::guessModelNamesUsing($callback);
    }
}
