<?php

namespace Tests\Unit\Observers;

use App\Models\AsaasCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Customer;
use App\Services\Asaas\AsaasCustomerService;
use App\Observers\CustomerObserver;
use Mockery;

class CustomerObserverTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->asaasService = Mockery::mock(AsaasCustomerService::class);
        $this->app->instance(AsaasCustomerService::class, $this->asaasService);
    }

    /**
     * @return void
     */
    public function test_customer_is_updated_on_asaas_when_updated_locally()
    {
        $customer = Customer::factory()->create();
        AsaasCustomer::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $customer->refresh();

        $this->assertNotNull($customer->asaasCustomer);

        $this->asaasService
            ->shouldReceive('updateCustomerOnAsaas')
            ->once()
            ->with(Mockery::on(function ($arg) use ($customer) {
                return $arg->id === $customer->id;
            }));

        $observer = new CustomerObserver($this->asaasService);
        $observer->updated($customer);
    }

    /**
     * @return void
     */
    public function test_customer_is_created_on_asaas_when_created_locally()
    {
        $customer = Customer::factory()->create();

        $this->asaasService
            ->shouldReceive('createCustomerOnAsaas')
            ->once()
            ->with(Mockery::on(function ($arg) use ($customer) {
                return $arg->id === $customer->id;
            }));

        $observer = new CustomerObserver($this->asaasService);
        $observer->created($customer);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
