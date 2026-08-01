<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('localized-second', fn () => 'localized second')
    ->name('localized-second');
