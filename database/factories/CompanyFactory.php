<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Database\Factories\Concerns\HasSpanishContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    use HasSpanishContent;

    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'name' => $this->faker->company(),
            'sector' => $this->faker->randomElement(['Tecnología', 'Salud', 'Finanzas', 'Educación', 'Manufactura']),
            'internal_tax_id' => $this->faker->numerify('ABC#######'),
            'is_verified' => $this->faker->boolean(80),
            'city' => $this->spanish(self::$spanishCities),
            'state' => 'Chiapas',
        ];
    }
}
