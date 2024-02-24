<?php

namespace Modules\Second\Console\Commands;

use Illuminate\Console\Command;
use Modules\Second\Models\Team;

class DeleteTeamsCommand extends Command
{
    protected $signature = 'second:delete-teams';

    protected $description = 'Delete All Teams';

    public function handle(): void
    {
        Team::truncate();
    }
}
