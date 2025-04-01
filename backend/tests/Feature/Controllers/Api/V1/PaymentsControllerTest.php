<?php

namespace Tests\Feature\Controllers\Api\V1;

use App\Models\AsaasCustomer;
use App\Models\Customer;
use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @return void
     */
    public function test_process_payment_successfully(): void
    {
        $customer = Customer::factory()->create();
        AsaasCustomer::factory()->create([
            'customer_id' => $customer->id,
            'asaas_id' => uniqid(),
        ]);

        $mockClient = $this->mockAsaasClientForBoleto();
        $this->app->instance(AsaasClient::class, $mockClient);

        $paymentData = [
            'customer_id' => $customer->id,
            'billing_type' => BillingTypeEnum::BOLETO->value,
            'value' => 100.00,
            'description' => 'Pagamento de teste',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'cpf_cnpj' => $customer->cpf_cnpj
            ],
        ];

        $response = $this->withoutExceptionHandling()->postJson(route('api.payments.process'), $paymentData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'uuid',
                    'customer_id',
                    'asaas_id',
                    'value',
                    'status',
                    'status_name',
                    'billing_type',
                    'billing_type_name',
                    'description',
                    'due_date',
                    'invoice_url',
                    'bank_slip_url',
                    'pix_qr_code',
                    'pix_copy_paste',
                    'created_at',
                    'updated_at'
                ]
            ]);

        $this->assertDatabaseHas('payments', [
            'customer_id' => $customer->id,
            'asaas_id' => 'pay_123456789',
            'value' => 100.00,
            'status' => PaymentStatusEnum::PENDING->value,
            'billing_type' => BillingTypeEnum::BOLETO->value
        ]);
    }

    /**
     * @return void
     */
    public function test_process_payment_with_pix_successfully(): void
    {
        $customer = Customer::factory()->create();
        $mockClient = $this->mockAsaasClientForPix();
        $this->app->instance(AsaasClient::class, $mockClient);

        AsaasCustomer::factory()->create([
            'customer_id' => $customer->id,
            'asaas_id' => uniqid(),
        ]);

        $paymentData = [
            'customer_id'   => $customer->id,
            'billing_type'  => BillingTypeEnum::PIX->value,
            'value'         => 150.00,
            'description'   => 'Pagamento de teste com PIX',
            'due_date' => now()->addDays(5)->format('Y-m-d'),
            'customer' => [
                'name' => $customer->name,
                'email' => $customer->email,
                'cpf_cnpj' => $customer->cpf_cnpj
            ],
        ];

        $response = $this->postJson(route('api.payments.process'), $paymentData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('payments', [
            'customer_id'   => $customer->id,
            'asaas_id'      => 'pay_123456789',
            'value'         => 150.00,
            'status'        => PaymentStatusEnum::PENDING->value,
            'billing_type'  => BillingTypeEnum::PIX->value
        ]);
    }

    /**
     * @return MockInterface
     */
    private function mockAsaasClientForBoleto(): MockInterface
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $mockClient->shouldReceive('post')
            ->with('customers', Mockery::any())
            ->andReturn([
                'id' => uniqid(),
                'dateCreated'   => '2025-03-28',
                'name'          => 'Cliente Teste',
                'email'         => 'email@teste.com',
                'cpfCnpj'       => '12345678901'
            ]);

        $mockClient->shouldReceive('post')
            ->with('payments', Mockery::any())
            ->once()
            ->andReturn([
                'id' =>         'pay_123456789',
                'dateCreated'   => '2025-03-28',
                'customer'      => uniqid(),
                'status'        => PaymentStatusEnum::PENDING->value,
                'invoiceUrl'    => 'https://example.com/invoice',
                'bankSlipUrl'   => 'https://example.com/bankslip',
                'value'         => 100.00,
                'netValue'      => 100.00,
                'billingType'   => BillingTypeEnum::BOLETO->value,
                'dueDate'       => '2025-04-02'
            ]);

        return $mockClient;
    }

    /**
     * @return MockInterface
     */
    private function mockAsaasClientForPix(): MockInterface
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $mockClient->shouldReceive('post')
            ->with('payments', Mockery::any())
            ->once()
            ->andReturn([
                'id'            => 'pay_123456789',
                'dateCreated'   => '2025-03-28',
                'customer'      => uniqid(),
                'status'        => PaymentStatusEnum::PENDING->value,
                'invoiceUrl'    => 'https://example.com/invoice',
                'value'         => 150.00,
                'netValue'      => 150.00,
                'billingType'   => BillingTypeEnum::PIX->value,
                'dueDate'       => '2025-04-02',
                'pixQrCodeUrl'  => 'https://example.com/qr',
                'pixCopiaECola' => 'pix-copy-paste'
            ]);

        return $mockClient;
    }
}
