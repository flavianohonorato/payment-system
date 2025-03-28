<?php

namespace App\Services\Asaas;

use App\Services\Asaas\Contracts\AsaasServiceInterface;
use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Exceptions\AsaasApiException;

class AsaasService implements AsaasServiceInterface
{
    public function __construct(protected AsaasClient $client) {}

    /**
     * @param CustomerDTO $customer
     * @return array
     * @throws AsaasApiException
     */
    public function createCustomer(CustomerDTO $customer): array
    {
        return $this->client->post('/api/v3/customers', $customer->toArray());
    }

    /**
     * @param string $customerId
     * @param CustomerDTO $customer
     * @return array
     * @throws AsaasApiException
     */
    public function updateCustomer(string $customerId, CustomerDTO $customer): array
    {
        return $this->client->put("/api/v3/customers/{$customerId}", $customer->toArray());
    }

    /**
     * @param string $customerId
     * @return array
     * @throws AsaasApiException
     */
    public function getCustomer(string $customerId): array
    {
        return $this->client->get("/api/v3/customers/{$customerId}");
    }

    /**
     * @param PaymentDTO $payment
     * @return array
     * @throws AsaasApiException
     */
    public function createPayment(PaymentDTO $payment): array
    {
        return $this->client->post('/api/v3/payments', $payment->toArray());
    }

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getPayment(string $paymentId): array
    {
        return $this->client->get("/api/v3/payments/{$paymentId}");
    }

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function cancelPayment(string $paymentId): array
    {
        return $this->client->delete("/api/v3/payments/{$paymentId}");
    }

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getBankSlipBarCode(string $paymentId): array
    {
        return $this->client->get("/api/v3/payments/{$paymentId}/identificationField");
    }

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getPixQrCode(string $paymentId): array
    {
        return $this->client->get("/api/v3/payments/{$paymentId}/pixQrCode");
    }
}
