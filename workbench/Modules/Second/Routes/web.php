<?php

use Illuminate\Support\Facades\Route;

Route::get('web-second', fn () => 'web second')
    ->name('web-second');
