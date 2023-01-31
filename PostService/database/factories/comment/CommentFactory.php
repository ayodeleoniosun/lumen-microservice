<?php

namespace Database\Factories\comment;

use App\Models\Comment\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => $this->faker->numberBetween(1, 50),
            'post_id' => $this->faker->numberBetween(1, 50),
            'comment' => $this->faker->sentence(10, true),
        ];
    }
}
