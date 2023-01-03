<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'author_id' => $this->faker->randomDigitNotNull(),
            'title' => $this->faker->title,
            'description' => $this->faker->text,
            'pages' => $this->faker->randomNumber(2),
            'isbn' => $this->faker->isbn10(),
            'price' => $this->faker->randomNumber(5),
        ];
    }
}
