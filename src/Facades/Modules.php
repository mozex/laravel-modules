<?php

namespace Mozex\Modules\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mozex\Modules\Modules
 */
class Modules extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mozex\Modules\Modules::class;
    }
}
