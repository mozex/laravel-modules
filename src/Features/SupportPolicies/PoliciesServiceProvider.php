<?php

namespace Mozex\Modules\Features\SupportPolicies;

use Illuminate\Contracts\Auth\Access\Gate as GateInstance;
use Illuminate\Support\Facades\Gate;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use Override;
use ReflectionMethod;
use ReflectionProperty;

class PoliciesServiceProvider extends Feature
{
    /** @var array<class-string, string> */
    private array $guesses = [];

    private ?ReflectionProperty $callbackProperty = null;

    private ?ReflectionMethod $guessMethod = null;

    public static function asset(): AssetType
    {
        return AssetType::Policies;
    }

    #[Override]
    public function boot(): void
    {
        $callback = function (string $modelName) use (&$callback) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return $this->guesses[$modelName] ??= sprintf(
                    '%s%s\\%s%sPolicy',
                    config('modules.modules_namespace'),
                    $module,
                    static::asset()->config()['namespace'] ?? 'Policies\\',
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
                $gate = $this->app->make(GateInstance::class);

                ($this->callbackProperty ??= new ReflectionProperty($gate, 'guessPolicyNamesUsingCallback'))
                    ->setValue($gate, null);

                return ($this->guessMethod ??= new ReflectionMethod($gate, 'guessPolicyName'))
                    ->invoke($gate, $modelName);
            } finally {
                // Re-register the same closure so per-call allocations and
                // closure-identity churn are avoided on the fallback path.
                Gate::guessPolicyNamesUsing($callback);
            }
        };

        Gate::guessPolicyNamesUsing($callback);
    }
}
