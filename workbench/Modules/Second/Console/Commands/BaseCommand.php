<?php

namespace Modules\Second\Console\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;

abstract class BaseCommand extends Command
{
    protected $signature = 'second:base-command';

    protected $description = 'Base Command';

    public function handle(): void
    {
        info('Base Command');
    }
}
