<?php

namespace Modules\First\Database\Seeders;

use Illuminate\Database\Seeder;

class FirstDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
        ]);
    }
}
