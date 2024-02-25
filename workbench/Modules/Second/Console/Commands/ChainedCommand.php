<?php

namespace Modules\Second\Console\Commands;

use function Laravel\Prompts\info;

class ChainedCommand extends BaseCommand
{
    protected $signature = 'second:chained-command';

    protected $description = 'Chained Command';

    public function handle(): void
    {
        info('Chained Command');
    }
}
