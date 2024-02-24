<?php

namespace Mozex\Modules\Commands;

use Illuminate\Console\Command;
use Mozex\Modules\Contracts\BaseScout;
use Spatie\StructureDiscoverer\Data\DiscoveredClass;
use Spatie\StructureDiscoverer\Discover;

use function Laravel\Prompts\progress;

class ClearCommand extends Command
{
    protected $signature = 'modules:clear';

    protected $description = 'Clear all module assets cache.';

    public function handle(): void
    {
        progress(
            label: 'Clearing Modules Cache',
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

                $progress->label("Clearing {$discoverer->asset()->title()}");

                $discoverer->cacheDriver()->forget($discoverer->identifier());
            },
        );
    }
}
