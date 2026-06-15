<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-6 months', 'now');

        return [
            'text' => fake()->realTextBetween(40, 280),
            'visibility' => fake()->randomElement(['public', 'public', 'public', 'private']),
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ];
    }
}
