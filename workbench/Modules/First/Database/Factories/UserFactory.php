<?php

namespace Modules\First\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\First\Models\User;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
        ];
    }
}
