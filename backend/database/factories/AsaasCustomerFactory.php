<?php

namespace Database\Factories;

use App\Models\AsaasCustomer;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

class AsaasCustomerFactory extends Factory
{
    protected $model = AsaasCustomer::class;

    /**
     * @return array
     */
    public function definition(): array
    {
        return [
            'customer_id'           => Customer::factory(),
            'asaas_id'              => 'cus_' . fake()->unique()->uuid,
            'date_created'          => fake()->date(),
            'external_reference'    => fake()->uuid,
            'notification_disabled' => false,
            'additional_emails'     => null,
            'person_type'           => fake()->randomElement(['FISICA', 'JURIDICA']),
            'deleted'               => false,
            'asaas_data' => [
                'id'            => 'cus_' . fake()->regexify('[A-Z0-9]{10}'),
                'name'          => fake()->name,
                'email'         => fake()->email,
                'phone'         => fake()->phoneNumber,
                'dateCreated'   => fake()->date(),
            ],
        ];
    }
}
