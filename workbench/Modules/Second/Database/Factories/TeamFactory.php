<?php

namespace Modules\Second\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Second\Models\Team;

class TeamFactory extends Factory
{
    protected $model = Team::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
