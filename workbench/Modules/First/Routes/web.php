<?php

use Illuminate\Support\Facades\Route;

Route::get('web-first', fn () => 'web first')
    ->name('web-first');
