<?php

namespace App\Services\Asaas\Contracts;

use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Exceptions\AsaasApiException;

interface AsaasServiceInterface
{
    /**
     * @param CustomerDTO $customer
     * @return array
     * @throws AsaasApiException
     */
    public function createCustomer(CustomerDTO $customer) : array;

    /**
     * @param string $customerId
     * @param CustomerDTO $customer
     * @return array
     * @throws AsaasApiException
     */
    public function updateCustomer(string $customerId, CustomerDTO $customer): array;

    /**
     * @param string $customerId
     * @return array
     * @throws AsaasApiException
     */
    public function getCustomer(string $customerId): array;

    /**
     * @param PaymentDTO $payment
     * @return array
     * @throws AsaasApiException
     */
    public function createPayment(PaymentDTO $payment): array;

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getPayment(string $paymentId): array;

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function cancelPayment(string $paymentId): array;

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getBankSlipBarCode(string $paymentId): array;

    /**
     * @param string $paymentId
     * @return array
     * @throws AsaasApiException
     */
    public function getPixQrCode(string $paymentId): array;
}
