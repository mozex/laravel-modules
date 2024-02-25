<?php

namespace Modules\First\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class FirstValidCommand extends Command
{
    protected $signature = 'first:valid';

    protected $description = 'First Valid Command';

    public function handle(): void
    {
        info('First Valid Command');
    }
}
