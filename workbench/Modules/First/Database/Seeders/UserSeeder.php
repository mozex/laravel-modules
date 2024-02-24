<?php

namespace Modules\First\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\First\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(10)->create();
    }
}
