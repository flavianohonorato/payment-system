<?php

namespace App\Observers;

use App\Models\Customer;
use App\Services\Asaas\AsaasCustomerService;
use Exception;

class CustomerObserver
{
    public function __construct(protected AsaasCustomerService $asaasCustomerService) {}

    /**
     * @param Customer $customer
     * @return void
     */
    public function created(Customer $customer): void
    {
        try {
            $this->asaasCustomerService->createCustomerOnAsaas($customer);
        } catch (Exception $e) {
            logger('Failed to create customer on Asaas', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * @param Customer $customer
     * @return void
     */
    public function updated(Customer $customer): void
    {
        try {
            if ($customer->asaasCustomer) {
                $this->asaasCustomerService->updateCustomerOnAsaas($customer);
            }
        } catch (Exception $e) {
            logger('Failed to update customer on Asaas', [
                'customer_id' => $customer->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
