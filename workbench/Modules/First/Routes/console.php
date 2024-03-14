<?php

use Illuminate\Support\Facades\Schedule;

Artisan::command('first:console-command-1', function () {
    $this->comment('first-command-1');
})->purpose('Testing first commands')->hourly();

Schedule::exec('first-scheduled-command-2')
    ->daily();

Schedule::exec('first-scheduled-command-3')
    ->daily();
