<?php

use Illuminate\Support\Facades\Schedule;

Artisan::command('second:console-command-1', function () {
    $this->comment('second-command-1');
})->purpose('Testing second commands')->hourly();

Schedule::exec('second-scheduled-command-3')
    ->daily();
