<?php

namespace Mozex\Modules\Features\SupportFactories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;
use ReflectionProperty;

class FactoriesServiceProvider extends Feature
{
    /** @var array<class-string, string> */
    private array $guesses = [];

    private ?ReflectionProperty $resolverProperty = null;

    public static function asset(): AssetType
    {
        return AssetType::Factories;
    }

    #[Override]
    public function boot(): void
    {
        $callback = function (string $modelName) use (&$callback) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return $this->guesses[$modelName] ??= sprintf(
                    '%s%s\\%s%sFactory',
                    config('modules.modules_namespace'),
                    $module,
                    static::asset()->config()['namespace'] ?? 'Database\\Factories\\',
                    str($modelName)->after(
                        sprintf(
                            '%s%s\\%s',
                            config('modules.modules_namespace'),
                            $module,
                            AssetType::Models->config()['namespace'] ?? 'Models\\'
                        )
                    )
                );
            }

            try {
                ($this->resolverProperty ??= new ReflectionProperty(Factory::class, 'factoryNameResolver'))
                    ->setValue(null, null);

                // The guesser contract only ever receives model class names.
                return is_subclass_of($modelName, Model::class)
                    ? Factory::resolveFactoryName($modelName)
                    : null;
            } finally {
                // Re-register the same closure so per-call allocations and
                // closure-identity churn are avoided on the fallback path.
                Factory::guessFactoryNamesUsing($callback);
            }
        };

        Factory::guessFactoryNamesUsing($callback);
    }
}
