<?php

use Illuminate\Support\Facades\Route;

Route::get('localized-second', fn () => 'localized second')
    ->name('localized-second');
