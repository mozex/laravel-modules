<?php

namespace Mozex\Modules\Concerns;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

trait SupportsFactory
{
    public function bootFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            if (Str::startsWith($modelName, 'Modules\\')) {
                $module = Str::before(Str::after($modelName, 'Modules\\'), '\\Models');
                $modelName = Str::after($modelName, 'Modules\\'.$module.'\\Models\\');
                $prefix = 'Modules\\'.$module.'\\';
            } else {
                $modelName = Str::after($modelName, 'App\\Models\\');
                $prefix = '';
            }

            return $prefix.'Database\\Factories\\'.$modelName.'Factory';
        });
    }
}
