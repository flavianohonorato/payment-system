<?php

namespace App\Services\Asaas;

use App\Models\AsaasCustomer;
use App\Models\Customer;
use App\Models\Payment;
use App\Services\Asaas\DTO\CustomerDTO;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use App\Services\Asaas\Exceptions\AsaasApiException;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
            ->whereJsonContains('asaas_data->cpfCnpj', $customerDTO->cpfCnpj)
            ->first();

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
     * @throws AsaasApiException|Exception
     */
    public function getOrCreateCustomer(array $customerData, ?int $customerId = null): string
    {
        if ($asaasId = $this->findCustomerByLocalId($customerId)) {
            return $asaasId;
        }

        $localCustomerInfo = $this->findCustomerByCpfCnpj($customerData['cpf_cnpj']);
        if ($localCustomerInfo && $localCustomerInfo['asaasId']) {
            return $localCustomerInfo['asaasId'];
        }

        if ($localCustomerInfo && $localCustomerInfo['customer']) {
            $customerId = $localCustomerInfo['customer']->id;
        }

        $asaasId = $this->findCustomerInAsaas($customerData);
        if ($asaasId) {
            return $asaasId;
        }

        return $this->createAndSaveCustomer(
            $customerData,
            $customerId, $localCustomerInfo['customer'] ?? null
        );
    }

    /**
     * @param int|null $customerId
     * @return string|null
     */
    private function findCustomerByLocalId(?int $customerId): ?string
    {
        if (!$customerId) {
            return null;
        }

        try {
            Customer::findOrFail($customerId);

            $asaasCustomer = AsaasCustomer::query()
                ->where('customer_id', $customerId)
                ->first();

            if ($asaasCustomer && !empty($asaasCustomer->asaas_id)) {
                return $asaasCustomer->asaas_id;
            }
        } catch (ModelNotFoundException $e) {
            logger('Cliente local não encontrado', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);
            return null;
        }

        return null;
    }

    /**
     * @param string $cpfCnpj
     * @return array|null
     */
    private function findCustomerByCpfCnpj(string $cpfCnpj): ?array
    {
        $customer = Customer::query()
            ->where('cpf_cnpj', $cpfCnpj)
            ->first();

        if (!$customer) {
            return null;
        }

        $asaasCustomer = AsaasCustomer::query()
            ->where('customer_id', $customer->id)
            ->first();

        return [
            'customer' => $customer,
            'asaasId' => $asaasCustomer && !empty($asaasCustomer->asaas_id) ? $asaasCustomer->asaas_id : null
        ];
    }

    /**
     * @param array $customerData
     * @return string|null
     */
    private function findCustomerInAsaas(array $customerData): ?string
    {
        $asaasCustomer = AsaasCustomer::query()
            ->whereJsonContains('asaas_data->cpfCnpj', $customerData['cpf_cnpj'])
            ->first();

        if (!$asaasCustomer) {
            return null;
        }

        $customer = Customer::query()
            ->where('cpf_cnpj', $customerData['cpf_cnpj'])
            ->first();

        if (!$customer) {
            $customer = $this->createLocalCustomer($customerData);
            $asaasCustomer->update(['customer_id' => $customer->id]);
        }

        return !empty($asaasCustomer->asaas_id) ? $asaasCustomer->asaas_id : null;
    }

    /**
     * @param array $customerData
     * @return Customer
     */
    private function createLocalCustomer(array $customerData): Customer
    {
        return Customer::create([
            'name'          => $customerData['name'],
            'email'         => $customerData['email'],
            'cpf_cnpj'      => $customerData['cpf_cnpj'],
            'external_id'   => $customerData['external_id'] ?? null,
        ]);
    }

    /**
     * @param array $customerData
     * @param int|null $customerId
     * @param Customer|null $customer
     * @return string
     * @throws AsaasApiException|Exception
     */
    private function createAndSaveCustomer(array $customerData, ?int $customerId, ?Customer $customer): string
    {
        $customerDTO = new CustomerDTO(
            name: $customerData['name'],
            email: $customerData['email'],
            cpfCnpj: $customerData['cpf_cnpj'],
            phone: $customerData['phone'] ?? null,
            address: $customerData['address'] ?? null,
            customerId: $customerId ?? ($customer?->id)
        );

        try {
            $response = $this->createCustomer($customerDTO);
            if (empty($response['id'])) {
                throw new AsaasApiException("ID do cliente não retornado pela API Asaas");
            }

            list($customer, $customerData) = $this->manageLocalCustomer($customer, $customerData);

            AsaasCustomer::create([
                'customer_id'       => $customer->id,
                'asaas_id'          => $response['id'],
                'date_created'      => $response['dateCreated'] ?? now(),
                'external_reference' => $response['externalReference'] ?? null,
                'person_type'       => $response['personType'] ?? 'FISICA',
                'asaas_data'        => $response,
            ]);

            return $response['id'];
        } catch (Exception $e) {
            logger("Erro ao criar cliente no Asaas", [
                'error' => $e->getMessage(),
                'customerData' => $customerData
            ]);

            throw $e;
        }
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
        $customerId = $paymentDTO->getAsaasCustomerId();

        if (empty($customerId)) {
            throw new AsaasApiException("ID do cliente Asaas não pode ser vazio");
        }

        $paymentData = [
            'customer'      => $customerId,
            'billingType'   => $paymentDTO->billingType->value,
            'value'         => $paymentDTO->value,
            'description'   => $paymentDTO->description,
            'dueDate'       => $paymentDTO->dueDate->format('Y-m-d'),
        ];

        try {
            return $this->asaasClient->post('payments', $paymentData);
        } catch (Exception $e) {
            logger("Erro ao criar pagamento no Asaas", [
                'error' => $e->getMessage(),
                'paymentData' => $paymentData
            ]);

            throw $e;
        }
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

    /**
     * @param Customer|null $customer
     * @param array $customerData
     * @return Customer
     */
    public function manageLocalCustomer(?Customer $customer, array $customerData): Customer
    {
        if ($customer) {
            if (empty($customer->external_id) && isset($customerData['external_id'])) {
                $customer->update(['external_id' => $customerData['external_id']]);
            }

            return $customer;
        }

        return $this->createLocalCustomer($customerData);
    }
}

