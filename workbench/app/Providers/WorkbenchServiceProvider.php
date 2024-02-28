<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Mozex\Modules\Facades\Modules;

class WorkbenchServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Modules::setBasePath(dirname(__DIR__, 2));
    }

    public function boot(): void
    {

    }
}
