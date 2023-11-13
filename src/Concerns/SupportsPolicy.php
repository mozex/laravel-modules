<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

trait SupportsPolicy
{
    public function bootPolicies(): void
    {
        Gate::guessPolicyNamesUsing(function (string $modelName) {
            if (Str::startsWith($modelName, 'Modules\\')) {
                $module = Str::before(Str::after($modelName, 'Modules\\'), '\\Models');
                $modelName = Str::after($modelName, 'Modules\\'.$module.'\\Models\\');
                $prefix = 'Modules\\'.$module.'\\';
            } else {
                $modelName = Str::after($modelName, 'App\\Models\\');
                $prefix = '';
            }

            return $prefix.'Policies\\'.$modelName.'Policy';
        });
    }
}
