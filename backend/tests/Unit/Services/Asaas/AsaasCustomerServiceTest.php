<?php

namespace Tests\Unit\Services\Asaas;

use App\Services\Asaas\Exceptions\AsaasApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\AsaasCustomerService;
use App\Models\Customer;
use App\Models\AsaasCustomer;
use Mockery;

class AsaasCustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->asaasClient = Mockery::mock(AsaasClient::class);
        $this->service = new AsaasCustomerService($this->asaasClient);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_create_customer_on_asaas()
    {
        $customer = Customer::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'cpf_cnpj' => '12345678900',
        ]);

        $asaasResponse = [
            'id'            => 'cus_000001',
            'dateCreated'   => '2023-01-01',
            'name'          => 'John Doe',
            'email'         => 'john@example.com',
            'cpfCnpj'       => '12345678900',
            'personType'    => 'FISICA',
        ];

        $this->asaasClient
            ->shouldReceive('post')
            ->once()
            ->with('customers', Mockery::any())
            ->andReturn($asaasResponse);

        $result = $this->service->createCustomerOnAsaas($customer);

        $this->assertInstanceOf(AsaasCustomer::class, $result);
        $this->assertEquals('cus_000001', $result->asaas_id);
        $this->assertEquals($customer->id, $result->customer_id);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_update_customer_on_asaas()
    {
        $customer = Customer::factory()->create();

        $asaasId = 'cus_' . uniqid();

        AsaasCustomer::factory()->create([
            'customer_id' => $customer->id,
            'asaas_id' => $asaasId,
        ]);

        $asaasResponse = [
            'id' => $asaasId,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
        ];

        $customer->refresh();

        $this->asaasClient
            ->shouldReceive('put')
            ->once()
            ->with('customers/' . $asaasId, Mockery::any())
            ->andReturn($asaasResponse);

        $result = $this->service->updateCustomerOnAsaas($customer);

        $this->assertInstanceOf(AsaasCustomer::class, $result);
        $this->assertEquals($asaasResponse, $result->asaas_data);
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
