<?php

namespace Database\Factories\Nested;

use Illuminate\Database\Eloquent\Factories\Factory;

class NestedTestFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
