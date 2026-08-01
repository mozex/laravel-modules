<?php

declare(strict_types=1);

namespace Modules\Second\Database\Seeders;

use Illuminate\Database\Seeder;

class SecondDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TeamDatabaseSeeder::class,
        ]);
    }
}
