<?php

namespace Mozex\Modules\Tests;

use Mozex\Modules\ModulesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            ModulesServiceProvider::class,
        ];
    }
}
