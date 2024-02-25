<?php

use Illuminate\Support\Facades\Route;

Route::get('undefined-second', fn () => 'undefined second')
    ->name('undefined-second');
