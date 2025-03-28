<?php

namespace App\Services\Asaas\DTO;

use App\Services\Asaas\Enums\BillingTypeEnum;
use Illuminate\Support\Carbon;

readonly class PaymentDTO
{
    public function __construct(
        public string $customer,
        public BillingTypeEnum $billingType,
        public float $value,
        public string $description,
        public Carbon $dueDate,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customer'      => $this->customer,
            'billingType'   => $this->billingType->value,
            'value'         => $this->value,
            'description'   => $this->description,
            'dueDate'       => $this->dueDate->format('Y-m-d'),
        ];
    }
}
