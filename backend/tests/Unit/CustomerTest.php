<?php

namespace Tests\Unit;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_be_created(): void
    {
        $name = 'John Doe';
        $customer = Customer::factory()->create(['name' => $name]);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals($name, $customer->name);
    }

    public function test_it_can_be_deleted()
    {
        $customer = Customer::factory()->create();
        $customerId = $customer->id;
        $customer->delete();

        $this->assertDatabaseMissing('customers', ['id' => $customerId]);
    }
}
