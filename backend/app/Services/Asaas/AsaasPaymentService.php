<?php

namespace App\Services\Asaas;

use App\Models\AsaasCustomer;
use App\Models\Payment;
use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use App\Services\Asaas\Exceptions\AsaasApiException;
use Exception;

class AsaasPaymentService
{
    /**
     * @param AsaasClient $asaasClient
     */
    public function __construct(protected AsaasClient $asaasClient) {}

    /**
     * @param CustomerDTO $customerDTO
     * @return array
     * @throws AsaasApiException
     */
    public function createCustomer(CustomerDTO $customerDTO): array
    {
        $existingCustomer = AsaasCustomer::query()
            ->where('asaas_id', $customerDTO->cpfCnpj)->
            first();

        if ($existingCustomer) {
            return $existingCustomer->toArray();
        }

        $customerData = [
            'name'      => $customerDTO->name,
            'email'     => $customerDTO->email,
            'cpfCnpj'   => $customerDTO->cpfCnpj,
        ];

        if (isset($customerDTO->phone)) {
            $customerData['phone'] = $customerDTO->phone;
            $customerData['mobilePhone'] = $customerDTO->phone;
        }

        if (isset($customerDTO->customerId)) {
            $customerData['externalReference'] = $customerDTO->customerId;
        }

        if (isset($customerDTO->address)) {
            $customerData['address'] = $customerDTO->address;
        }

        try {
            $response = $this->asaasClient->post('customers', $customerData);

            if (!isset($response['dateCreated'])) {
                $response['dateCreated'] = now()->format('Y-m-d');
            }

            return $response;
        } catch (Exception $e) {
            logger('Erro ao criar cliente no Asaas', [
                'customer_data' => $customerData,
                'error' => $e->getMessage()
            ]);

            throw new AsaasApiException("Erro ao criar cliente no Asaas: " . $e->getMessage());
        }
    }

    /**
     * @param array $customerData
     * @param int|null $customerId
     * @return string
     * @throws AsaasApiException
     */
    public function getOrCreateCustomer(array $customerData, ?int $customerId = null): string
    {
        if ($customerId) {
            $asaasCustomer = AsaasCustomer::query()
                ->where('customer_id', $customerId)
                ->first();

            if ($asaasCustomer) {
                return $asaasCustomer->asaas_id;
            }
        }

        $asaasCustomer = AsaasCustomer::query()
            ->where('cpf_cnpj', $customerData['cpf_cnpj'])
            ->first();

        if ($asaasCustomer) {
            return $asaasCustomer->asaas_id;
        }

        $customerDTO = new CustomerDTO(
            name: $customerData['name'],
            email: $customerData['email'],
            cpfCnpj: $customerData['cpf_cnpj']
        );

        $response = $this->createCustomer($customerDTO);

        return $response['id'];
    }

    /**
     * @param PaymentDTO $paymentDTO
     * @return Payment
     * @throws AsaasApiException
     */
    public function processPayment(PaymentDTO $paymentDTO): Payment
    {
        $asaasResponse = $this->createPaymentOnAsaas($paymentDTO);

        return $this->createPaymentRecord($paymentDTO, $asaasResponse);
    }

    /**
     * @param PaymentDTO $paymentDTO
     * @return array
     * @throws AsaasApiException
     */
    private function createPaymentOnAsaas(PaymentDTO $paymentDTO): array
    {
        $paymentData = [
            'customer'      => $paymentDTO->getCustomerId(),
            'billingType'   => $paymentDTO->billingType->value,
            'value'         => $paymentDTO->value,
            'description'   => $paymentDTO->description,
            'dueDate'       => $paymentDTO->dueDate->format('Y-m-d'),
        ];

        return $this->asaasClient->post('payments', $paymentData);
    }

    /**
     * @param PaymentDTO $paymentDTO
     * @param array $asaasResponse
     * @return Payment
     */
    private function createPaymentRecord(PaymentDTO $paymentDTO, array $asaasResponse): Payment
    {
        return Payment::query()
            ->create([
                'customer_id'       => $paymentDTO->getCustomerId(),
                'asaas_id'          => $asaasResponse['id'],
                'value'             => $paymentDTO->value,
                'status'            => PaymentStatusEnum::PENDING->value,
                'billing_type'      => $paymentDTO->billingType->value,
                'description'       => $paymentDTO->description,
                'due_date'          => $paymentDTO->dueDate,
                'invoice_url'       => $asaasResponse['invoiceUrl'] ?? null,
                'bank_slip_url'     => $asaasResponse['bankSlipUrl'] ?? null,
                'pix_qr_code'       => $asaasResponse['pixQrCodeUrl'] ?? null,
                'pix_copy_paste'    => $asaasResponse['pixCopiaECola'] ?? null,
            ]);
    }
}

