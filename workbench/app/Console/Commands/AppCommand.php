<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class AppCommand extends Command
{
    protected $signature = 'app:command';

    protected $description = 'App Command';

    public function handle(): void
    {
        info('App Command');
    }
}
