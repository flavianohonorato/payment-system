<?php

namespace App\Services\Asaas;

use App\Services\Asaas\Exceptions\AsaasApiException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AsaasClient
{
    protected string $apiKey;
    protected string $apiUrl;
    protected Client $client;

    /**
     * @param Client|null $client
     */
    public function __construct(Client $client = null)
    {
        $this->apiKey = config('asaas.api_key');
        $this->apiUrl = config('asaas.api_url');

        if (config('asaas.sandbox')) {
            $this->apiUrl = str_replace('api.', 'api-sandbox.', $this->apiUrl);
        }

        $this->client = $client ?? $this->createClient();
    }

    /**
     * @return Client
     */
    protected function createClient(): Client
    {
        return new Client([
            'base_uri' => $this->apiUrl,
            'headers' => [
                'access_token' => $this->apiKey,
                'Content-Type' => 'application/json'
            ]
        ]);
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws AsaasApiException
     */
    public function get(string $endpoint): array
    {
        return $this->request('GET', $endpoint);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws AsaasApiException
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws AsaasApiException
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * @param string $endpoint
     * @return array
     * @throws AsaasApiException
     */
    public function delete(string $endpoint): array
    {
        return $this->request('DELETE', $endpoint);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $options
     * @return array
     * @throws AsaasApiException
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        try {
            $endpoint = '/v3/' . ltrim($endpoint, '/');
            $response = $this->client->request($method, $endpoint, $options);
            $contents = $response->getBody()->getContents();

            if (empty($contents)) {
                return [];
            }

            $data = json_decode($contents, true);

            return is_array($data) ? $data : [];

        } catch (GuzzleException $e) {
            return $this->handleRequestError($e);
        }
    }

    /**
     * @param GuzzleException $e
     * @return array
     * @throws AsaasApiException
     */
    protected function handleRequestError(GuzzleException $e): array
    {
        $message = $e->getMessage();
        $code = $e->getCode();

        if (method_exists($e, 'getResponse') && $e->getResponse()) {
            $response = $e->getResponse();
            $body = json_decode($response->getBody()->getContents(), true);

            if (isset($body['errors'][0]['description'])) {
                $message = $body['errors'][0]['description'];
            }

            $code = $response->getStatusCode();
        }

        logger('Asaas API Error', [
            'code' => $code,
            'message' => $message,
            'trace' => $e->getTraceAsString(),
        ]);

        throw new AsaasApiException($message, $code, $e);
    }
}
