<?php

namespace Modules\First\Console\Commands;

use App\Console\Commands\AppCommand;

use function Laravel\Prompts\info;

class ExtendedCommand extends AppCommand
{
    protected $signature = 'first:extended';

    protected $description = 'Extended Command';

    public function handle(): void
    {
        info('Extended Command');
    }
}
