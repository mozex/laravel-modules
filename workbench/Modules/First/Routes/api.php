<?php

use Illuminate\Support\Facades\Route;

Route::get('api-first', fn () => 'api first')
    ->name('api-first');
