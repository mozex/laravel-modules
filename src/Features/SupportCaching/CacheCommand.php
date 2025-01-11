<?php

namespace Mozex\Modules\Features\SupportCaching;

use Illuminate\Console\Command;
use Laravel\Prompts\Progress;
use Mozex\Modules\Contracts\BaseScout;
use Mozex\Modules\Enums\AssetType;

use function Laravel\Prompts\progress;

class CacheCommand extends Command
{
    protected $signature = 'modules:cache';

    protected $description = 'Cache all module assets.';

    public function handle(): void
    {
        progress(
            label: 'Caching Modules',
            steps: AssetType::activeScouts(),
            callback: function (BaseScout $scout, Progress $progress): void {
                $progress->label("Caching {$scout->asset()->title()}");

                $scout->clear();

                $scout->cache();
            },
        );
    }
}
