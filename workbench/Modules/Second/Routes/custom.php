<?php

use Illuminate\Support\Facades\Route;

Route::get('custom-second', fn () => 'custom second')
    ->name('custom-second');
