<?php

namespace App\Services\Asaas;

use App\Models\Customer;
use App\Models\AsaasCustomer;
use App\Services\Asaas\Exceptions\AsaasApiException;

class AsaasCustomerService
{
    public function __construct(protected AsaasClient $asaasClient) {}

    /**
     * @param Customer $customer
     * @return mixed
     * @throws AsaasApiException
     */
    public function createCustomerOnAsaas(Customer $customer): mixed
    {
        $customerData = $this->prepareCustomerData($customer);

        $response = $this->asaasClient->post('customers', $customerData);

        return AsaasCustomer::create([
            'customer_id'       => $customer->id,
            'asaas_id'          => $response['id'],
            'date_created'      => $response['dateCreated'],
            'external_reference' => (string) $customer->id,
            'person_type'       => $response['personType'] ?? null,
            'asaas_data'        => $response,
        ]);
    }

    /**
     * @param Customer $customer
     * @return mixed
     * @throws AsaasApiException
     */
    public function updateCustomerOnAsaas(Customer $customer): mixed
    {
        if (!$customer->asaasCustomer) {
            return $this->createCustomerOnAsaas($customer);
        }

        $customerData = $this->prepareCustomerData($customer);

        $response = $this->asaasClient->put(
            'customers/' . $customer->asaasCustomer->asaas_id,
            $customerData
        );

        $customer->asaasCustomer->update([
            'asaas_data' => $response,
        ]);

        return $customer->asaasCustomer;
    }

    /**
     * @param string $asaasId
     * @return array
     * @throws AsaasApiException
     */
    public function fetchCustomerFromAsaas(string $asaasId): array
    {
        return $this->asaasClient->get('customers/' . $asaasId);
    }

    /**
     * @param Customer $customer
     * @return mixed|null
     */
    public function syncCustomerData(Customer $customer): mixed
    {
        if (!$customer->asaasCustomer) {
            return null;
        }

        try {
            $response = $this->fetchCustomerFromAsaas($customer->asaasCustomer->asaas_id);

            $customer->asaasCustomer->update([
                'deleted'               => $response['deleted'] ?? false,
                'notification_disabled' => $response['notificationDisabled'] ?? false,
                'additional_emails'     => $response['additionalEmails'] ?? null,
                'asaas_data'            => $response,
            ]);

            return $customer->asaasCustomer;
        } catch (AsaasApiException $e) {
            logger('Failed to sync customer data from Asaas', [
                'customer_id'   => $customer->id,
                'asaas_id'      => $customer->asaasCustomer->asaas_id,
                'error'         => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * @param Customer $customer
     * @return array
     */
    protected function prepareCustomerData(Customer $customer): array
    {
        return [
            'name'              => $customer->name,
            'email'             => $customer->email,
            'phone'             => $customer->phone ?? null,
            'mobilePhone'       => $customer->mobile_phone ?? $customer->phone ?? null,
            'cpfCnpj'           => $customer->cpf_cnpj,
            'externalReference' => (string) $customer->id,
            'address'           => $customer->address ?? null,
            'addressNumber'     => $customer->address_number ?? null,
            'complement'        => $customer->complement ?? null,
            'province'          => $customer->neighborhood ?? null,
            'postalCode'        => $customer->postal_code ?? null,
        ];
    }
}
