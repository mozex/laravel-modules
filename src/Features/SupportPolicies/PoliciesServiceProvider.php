<?php

namespace Mozex\Modules\Features\SupportPolicies;

use Illuminate\Contracts\Auth\Access\Gate as GateInstance;
use Illuminate\Support\Facades\Gate;
use Mozex\Modules\Enums\AssetType;
use Mozex\Modules\Facades\Modules;
use Mozex\Modules\Features\Feature;
use ReflectionMethod;
use ReflectionProperty;

class PoliciesServiceProvider extends Feature
{
    public static function asset(): AssetType
    {
        return AssetType::Policies;
    }

    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(function (string $modelName) {
            if ($module = Modules::moduleNameFromNamespace($modelName)) {
                return sprintf(
                    '%s%s\\%s%sPolicy',
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
                $gate = $this->app->make(GateInstance::class);

                (new ReflectionProperty($gate, 'guessPolicyNamesUsingCallback'))
                    ->setValue($gate, null);

                $reflection = (new ReflectionMethod($gate, 'guessPolicyName'));

                return $reflection->invoke($gate, $modelName);
            } finally {
                $this->boot();
            }
        });
    }
}
