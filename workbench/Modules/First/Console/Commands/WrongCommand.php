<?php

namespace Modules\First\Console\Commands;

use function Laravel\Prompts\info;

class WrongCommand
{
    protected $signature = 'first:wrong-command';

    protected $description = 'Wrong command description.';

    public function handle(): void
    {
        info('Wrong Command');
    }
}
