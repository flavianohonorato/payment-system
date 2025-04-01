<?php

namespace Tests\Feature\Services\Asaas;

use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\AsaasService;
use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use App\Services\Asaas\Exceptions\AsaasApiException;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class AsaasServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $mock = new MockHandler([
            new Response(200, [], json_encode([
                'id' => 'cus_000000000001',
                'name' => 'Test Customer',
            ])),
        ]);

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->instance(Client::class, $client);

        $this->instance(
            AsaasClient::class,
            Mockery::mock(AsaasClient::class, function ($mock) {
                $mock->shouldReceive('post')
                    ->with('customers', Mockery::any())
                    ->andReturn([
                        'id'    => 'cus_000000000001',
                        'name'  => 'Test Customer',
                    ]);
            })
        );
    }

    /**
     * @return void
     * @throws AsaasApiException
     */
    public function test_create_customer()
    {
        $mockClient = Mockery::mock(AsaasClient::class);

        $customerDTO = new CustomerDTO(
            name: 'Test Customer',
            email: 'test@example.com',
            cpfCnpj: '12345678909'
        );

        $expectedResponse = [
            'id' => 'cus_123',
            'name' => 'Test Customer',
            'email' => 'test@example.com',
            'cpfCnpj' => '12345678909'
        ];

        $mockClient->shouldReceive('post')
            ->once()
            ->with('/v3/customers', [
                'name' => 'Test Customer',
                'email' => 'test@example.com',
                'cpfCnpj' => '12345678909',
                'phone' => null,
                'address' => null
            ])
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

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

        $paymentDTO = new PaymentDTO(
            asaasCustomerId: 'cus_123',
            customerId: 123,
            billingType: BillingTypeEnum::BOLETO,
            value: 100.50,
            description: 'Test payment',
            dueDate: new DateTime('2025-04-05')
        );

        $expectedResponse = [
            'id'            => 'pay_123456789',
            'customer'      => 'cus_123',
            'value'         => 100.50,
            'status'        => PaymentStatusEnum::PENDING->value,
            'billingType'   => BillingTypeEnum::BOLETO->value,
            'dueDate'       => '2025-04-05',
            'invoiceUrl'    => 'https://example.com/invoice',
            'bankSlipUrl'   => 'https://example.com/bankslip'
        ];

        $mockClient->shouldReceive('post')
            ->once()
            ->withArgs(function (string $endpoint, array $data) {
                return $endpoint === '/v3/payments'
                    && $data === [
                        'customer'      => 'cus_123',
                        'billingType'   => BillingTypeEnum::BOLETO->value,
                        'value'         => 100.50,
                        'description'   => 'Test payment',
                        'dueDate'       => '2025-04-05'
                    ];
            })
            ->andReturn($expectedResponse);



        $service = new AsaasService($mockClient);

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
            'id'            => $paymentId,
            'customer'      => 'cus_123',
            'billingType'   => BillingTypeEnum::BOLETO->value,
            'value'         => 100.50,
            'status'        => PaymentStatusEnum::PENDING->value,
        ];

        $mockClient->shouldReceive('get')
            ->once()
            ->with("/v3/payments/{$paymentId}")
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
            ->with("/v3/payments/{$paymentId}")
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
            ->with("/v3/payments/{$paymentId}/identificationField")
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
            ->with("/v3/payments/{$paymentId}/pixQrCode")
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

        $customerDTO = new CustomerDTO(
            name: 'Updated Customer',
            email: 'updated@example.com',
            cpfCnpj: '12345678901'
        );

        $expectedResponse = [
            'id'        => 'cus_123',
            'name'      => 'Updated Customer',
            'email'     => 'updated@example.com',
            'cpfCnpj'   => '12345678901'
        ];

        $mockClient->shouldReceive('put')
            ->once()
            ->with('/v3/customers/cus_123', [
                'name'      => 'Updated Customer',
                'email'     => 'updated@example.com',
                'cpfCnpj'   => '12345678901',
                'phone'     => null,
                'address'   => null
            ])
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);
        $response = $service->updateCustomer('cus_123', $customerDTO);

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
            ->with("/v3/customers/{$customerId}")
            ->andReturn($expectedResponse);

        $service = new AsaasService($mockClient);

        $response = $service->getCustomer($customerId);

        $this->assertEquals($expectedResponse, $response);
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
