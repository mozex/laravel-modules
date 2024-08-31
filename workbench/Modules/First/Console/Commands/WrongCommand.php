<?php

namespace Modules\First\Console\Commands;

use function Laravel\Prompts\info;

class WrongCommand
{
    protected string $signature = 'first:wrong-command';

    protected string $description = 'Wrong command description.';

    public function handle(): void
    {
        info('Wrong Command');
    }
}
