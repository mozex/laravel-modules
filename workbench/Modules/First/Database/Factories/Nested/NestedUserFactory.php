<?php

declare(strict_types=1);

namespace Modules\First\Database\Factories\Nested;

use Illuminate\Database\Eloquent\Factories\Factory;

class NestedUserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
