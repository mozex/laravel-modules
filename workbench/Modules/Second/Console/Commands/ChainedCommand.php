<?php

namespace Modules\Second\Console\Commands;

use Override;

use function Laravel\Prompts\info;

class ChainedCommand extends BaseCommand
{
    protected $signature = 'second:chained-command';

    protected $description = 'Chained Command';

    #[Override]
    public function handle(): void
    {
        info('Chained Command');
    }
}
