<?php

namespace Mozex\Modules\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Mozex\Modules\ModulesServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ModulesServiceProvider::class,
        ];
    }
}
