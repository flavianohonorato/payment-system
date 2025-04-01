<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * @var class-string<Model>
     */
    protected $model = Customer::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fakerBr = \Faker\Factory::create('pt_BR');

        return [
            'name'          => fake()->name(),
            'email'         => fake()->unique()->safeEmail(),
            'cpf_cnpj'      => $fakerBr->cpf(false),
            'phone'         => preg_replace('/[^0-9]/', '', $fakerBr->phoneNumber),
            'address'       => fake()->address(),
            'external_id'   => fake()->uuid(),

        ];
    }
}
