<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('web-first', fn () => 'web first')
    ->name('web-first');
