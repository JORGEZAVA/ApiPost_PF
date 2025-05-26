<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $arrayTipo = ["perro","gato"];
    protected $razas = [
        'perro' => ['Labrador', 'Bulldog', 'Chihuahua', 'Pastor Alemán', 'Pug'],
        'gato'  => ['Siamés', 'Persa', 'Bengala', 'Maine Coon', 'Esfinge']
    ];

    public function definition(): array
    {
        $tipoAnimal = fake()->randomElement($this->arrayTipo);
        $raza = fake()->randomElement($this->razas[$tipoAnimal]);

        return [
            'nameAnimal' => fake()->name(),
            'typeAnimal' => $tipoAnimal,
            'race' => $raza,
            'image' => fake()->image(),
            'description' => fake()->text(200),
            'user_id' => 1,
            'created_at' => $this->faker->dateTimeBetween('-2 month', 'now'),
            'updated_at' => now(),
            "verificado" => true,
        ];
    }
}
