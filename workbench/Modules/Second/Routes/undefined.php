<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('undefined-second', fn () => 'undefined second')
    ->name('undefined-second');
