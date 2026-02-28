<?php

use Illuminate\Support\Facades\Route;
use Modules\First\Livewire\Teams;

Route::get('web-first', fn () => 'web first')
    ->name('web-first');

Route::livewire('teams', Teams::class)
    ->name('teams');

Route::livewire('teams-by-name', 'first::teams')
    ->name('teams-by-name');

Route::livewire('counter', 'first::counter')
    ->name('counter');

Route::livewire('toggle', 'first::toggle')
    ->name('toggle');
