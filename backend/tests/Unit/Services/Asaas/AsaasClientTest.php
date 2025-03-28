<?php

namespace Tests\Unit\Services\Asaas;

use App\Services\Asaas\AsaasClient;
use App\Services\Asaas\Exceptions\AsaasApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Tests\TestCase;

class AsaasClientTest extends TestCase
{
    protected AsaasClient $asaasClient;
    protected MockHandler $mockHandler;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler();
        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->asaasClient = new AsaasClient($client);
    }

    /**
     * @return void
     */
    public function test_get_request()
    {
        $this->mockHandler->append(
            new Response(200, [
                'Content-Type' => 'application/json'
            ], json_encode(['id' => '123', 'name' => 'Test Customer']))
        );

        $result = $this->asaasClient->get('/customers/123');

        $this->assertIsArray($result);
        $this->assertEquals('123', $result['id']);
        $this->assertEquals('Test Customer', $result['name']);
    }

    /**
     * @return void
     */
    public function test_post_request()
    {
        $this->mockHandler->append(
            new Response(200, [
                'Content-Type' => 'application/json'
            ],json_encode(['id' => '456', 'name' => 'New Customer']))
        );

        $data = [
            'name' => 'New Customer',
            'email' => 'new@customer.com'
        ];

        $result = $this->asaasClient->post('/customers', $data);

        $this->assertIsArray($result);
        $this->assertEquals('456', $result['id']);
        $this->assertEquals('New Customer', $result['name']);
    }

    /**
     * @return void
     */
    public function test_put_request()
    {
        $this->mockHandler->append(
            new Response(200, ['Content-Type' => 'application/json'],
                json_encode(['id' => '123', 'name' => 'Updated Customer']))
        );

        $data = [
            'name' => 'Updated Customer'
        ];

        $result = $this->asaasClient->put('/customers/123', $data);

        $this->assertIsArray($result);
        $this->assertEquals('123', $result['id']);
        $this->assertEquals('Updated Customer', $result['name']);
    }

    /**
     * @return void
     */
    public function test_delete_request()
    {
        $this->mockHandler->append(
            new Response(200, [
                'Content-Type' => 'application/json'
            ], json_encode(['deleted' => true]))
        );

        $result = $this->asaasClient->delete('/customers/123');

        $this->assertIsArray($result);
        $this->assertTrue($result['deleted']);
    }

    /**
     * @return void
     */
    public function test_handle_request_error()
    {
        $this->mockHandler->append(
            new RequestException(
                'Error Communicating with Server',
                new Request('GET', '/customers/999'),
                new Response(404, [], json_encode([
                    'errors' => [
                        ['description' => 'Customer not found']
                    ]
                ]))
            )
        );

        $this->expectException(AsaasApiException::class);

        $this->asaasClient->get('/customers/999');
    }

    /**
     * @return void
     */
    public function test_empty_response()
    {
        $this->mockHandler->append(
            new Response(204, [], '')
        );

        $result = $this->asaasClient->delete('/customers/123');

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
