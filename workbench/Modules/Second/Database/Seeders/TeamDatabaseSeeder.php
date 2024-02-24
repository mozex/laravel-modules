<?php

namespace Modules\Second\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Second\Models\Team;

class TeamDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Team::factory(10)->create();
    }
}
