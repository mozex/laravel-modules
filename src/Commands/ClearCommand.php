<?php

namespace Mozex\Modules\Commands;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Enums\AssetType;

use function Laravel\Prompts\progress;

class ClearCommand extends Command
{
    protected $signature = 'modules:clear';

    protected $description = 'Clear all module assets cache.';

    public function handle(): void
    {
        progress(
            label: 'Clearing Modules Cache',
            steps: AssetType::activeScouts(),
            callback: function (BaseScout $scout, $progress): void {
                $progress->label("Clearing {$scout->asset()->title()}");

                $scout->clear();
            },
        );
    }
}
