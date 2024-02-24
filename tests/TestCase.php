<?php

namespace Mozex\Modules\Tests;

use App\Providers\WorkbenchServiceProvider;
use Livewire\LivewireServiceProvider;
use Mozex\Modules\ModulesServiceProvider;
use Orchestra\Testbench\Concerns\WithWorkbench;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    use WithWorkbench;

    protected function getPackageProviders($app): array
    {
        return [
            WorkbenchServiceProvider::class,
            LivewireServiceProvider::class,
            ModulesServiceProvider::class,
        ];
    }
}
