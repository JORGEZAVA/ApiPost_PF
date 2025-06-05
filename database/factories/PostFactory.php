<?php

namespace Database\Factories;

use App\Models\Post;
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

  
        $vaccineNames = array_keys(Post::$VACCINES);

  
        $cantidadAleatoria = fake()->numberBetween(0, count($vaccineNames));
        $seleccionadas     = fake()->randomElements($vaccineNames, $cantidadAleatoria);

     
        $mask = 0;
        foreach ($seleccionadas as $vacuna) {
            $mask |= Post::$VACCINES[$vacuna];
        }

        return [
            'nameAnimal' => fake()->name(),
            'typeAnimal' => $tipoAnimal,
            'race' => $raza,
            'image' => fake()->image(),
            'description' => fake()->text(200),
            'user_id' => 1,
            'created_at' => $this->faker->dateTimeBetween('-2 month', 'now'),
            'updated_at' => now(),
            "verificado" => false,
            'vaccines_mask'=> $mask,     
        ];
    }
}
