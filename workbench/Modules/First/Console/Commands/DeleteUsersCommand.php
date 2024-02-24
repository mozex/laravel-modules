<?php

namespace Modules\First\Console\Commands;

use Illuminate\Console\Command;
use Modules\First\Models\User;

class DeleteUsersCommand extends Command
{
    protected $signature = 'first:delete-users';

    protected $description = 'Delete All Users';

    public function handle(): void
    {
        User::truncate();
    }
}
