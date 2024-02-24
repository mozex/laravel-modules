<?php

namespace Mozex\Modules\Commands;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\BaseScout;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

use function Laravel\Prompts\progress;

class CacheCommand extends Command
{
    protected $signature = 'modules:cache';

    protected $description = 'Cache all module assets.';

    public function handle(): void
    {
        progress(
            label: 'Caching Modules',
            steps: Discover::in(__DIR__.'/../')
                ->classes()
                ->extending(BaseScout::class)
                ->custom(fn (DiscoveredClass $structure) => ! $structure->isAbstract)
                ->get(),
            callback: function (string $scout, $progress) {
                /** @var BaseScout $discoverer */
                $discoverer = app($scout);

                if ($discoverer->asset()->isDeactive()) {
                    return;
                }

                $progress->label("Caching {$discoverer->asset()->title()}");

                $discoverer->cacheDriver()->forget($discoverer->identifier());

                $discoverer->cache();
            },
        );
    }
}
