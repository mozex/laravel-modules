<?php

namespace Modules\Second\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class SecondValidCommand extends Command
{
    protected $signature = 'second:valid';

    protected $description = 'Second Valid Command';

    public function handle(): void
    {
        info('Second Valid Command');
    }
}
