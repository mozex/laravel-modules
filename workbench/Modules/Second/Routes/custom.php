<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('custom-second', fn () => 'custom second')
    ->name('custom-second');
