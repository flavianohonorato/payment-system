<?php

namespace Tests\Feature\Services\Asaas;

use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\AsaasService;
use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use App\Services\Asaas\Exceptions\AsaasApiException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Mockery;
use Tests\TestCase;

class AsaasServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_create_customer()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $customerData = [
            'name'      => 'Test Customer',
            'email'     => 'test@example.com',
            'cpfCnpj'   => '12345678909',
        ];

        $expectedResponse = [
            'id'        => 'cus_123',
            'name'      => 'Test Customer',
            'email'     => 'test@example.com',
            'cpfCnpj'   => '12345678909',
        ];

        $mockClient->shouldReceive('post')
            ->once()
            ->with('/api/v3/customers', $customerData)
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $customerDTO = new CustomerDTO(
            name: 'Test Customer',
            email: 'test@example.com',
            cpfCnpj: '12345678909'
        );

        $response = $service->createCustomer($customerDTO);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_create_payment()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $dueDate = Carbon::now()->addDays(5);

        $paymentData = [
            'customer'      => 'cus_123',
            'billingType'   => 'BOLETO',
            'value'         => 100.50,
            'description'   => 'Test payment',
            'dueDate'       => $dueDate->format('Y-m-d'),
        ];

        $expectedResponse = [
            'id'            => 'pay_123',
            'customer'      => 'cus_123',
            'billingType'   => BillingTypeEnum::BOLETO,
            'value'         => 100.50,
            'status'        => PaymentStatusEnum::PENDING,
            'dueDate'       => $dueDate->format('Y-m-d'),
            'invoiceUrl'    => 'https://asaas.com/invoice',
            'bankSlipUrl'   => 'https://asaas.com/bankslip',
        ];

        $mockClient->shouldReceive('post')
            ->once()
            ->with('/api/v3/payments', $paymentData)
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $paymentDTO = new PaymentDTO(
            customer: 'cus_123',
            billingType: BillingTypeEnum::BOLETO,
            value: 100.50,
            description: 'Test payment',
            dueDate: $dueDate
        );

        $response = $service->createPayment($paymentDTO);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_get_payment()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $paymentId = 'pay_123';

        $expectedResponse = [
            'id' => $paymentId,
            'customer'  => 'cus_123',
            'billingType' => BillingTypeEnum::BOLETO,
            'value'     => 100.50,
            'status'    => PaymentStatusEnum::PENDING,
        ];

        $mockClient->shouldReceive('get')
            ->once()
            ->with("/api/v3/payments/{$paymentId}")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->getPayment($paymentId);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_cancel_payment()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $paymentId = 'pay_123';

        $expectedResponse = [
            'id' => $paymentId,
            'deleted' => true
        ];

        $mockClient->shouldReceive('delete')
            ->once()
            ->with("/api/v3/payments/{$paymentId}")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->cancelPayment($paymentId);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_get_bank_slip_bar_code()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $paymentId = 'pay_123';

        $expectedResponse = [
            'identificationField' => '34191.09008 76547.834762 47839.823401 7 12345678901112'
        ];

        $mockClient->shouldReceive('get')
            ->once()
            ->with("/api/v3/payments/{$paymentId}/identificationField")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->getBankSlipBarCode($paymentId);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_get_pix_qr_code()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $paymentId = 'pay_123';

        $expectedResponse = [
            'encodedImage'  => 'base64_encoded_image_data',
            'payload'       => '00020101021226870014br.gov.bcb.pix2565qrcode-pix.asaas.com/pix/abcdef1234'
        ];

        $mockClient->shouldReceive('get')
            ->once()
            ->with("/api/v3/payments/{$paymentId}/pixQrCode")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->getPixQrCode($paymentId);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_update_customer()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $customerId = 'cus_123';

        $customerData = [
            'name' => 'Updated Customer',
            'email' => 'updated@example.com',
        ];

        $expectedResponse = [
            'id'    => $customerId,
            'name'  => 'Updated Customer',
            'email' => 'updated@example.com',
        ];

        $mockClient->shouldReceive('put')
            ->once()
            ->with("/api/v3/customers/{$customerId}", $customerData)
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $customerDTO = new CustomerDTO(
            name: 'Updated Customer',
            email: 'updated@example.com'
        );

        $response = $service->updateCustomer($customerId, $customerDTO);

        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_get_customer()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $customerId = 'cus_123';

        $expectedResponse = [
            'id'        => $customerId,
            'name'      => 'Test Customer',
            'email'     => 'test@example.com',
            'cpfCnpj'   => '12345678909',
        ];

        $mockClient->shouldReceive('get')
            ->once()
            ->with("/api/v3/customers/{$customerId}")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->getCustomer($customerId);

        $this->assertEquals($expectedResponse, $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
