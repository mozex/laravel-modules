<?php

namespace Modules\Second\Database\Factories\Nested;

use Illuminate\Database\Eloquent\Factories\Factory;

class NestedTeamFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
