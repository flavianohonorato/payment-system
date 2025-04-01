<?php

namespace App\Services\Asaas\DTO;

use App\Services\Asaas\Enums\BillingTypeEnum;
use DateTimeInterface;

readonly class PaymentDTO
{
    /**
     * @param string $asaasCustomerId
     * @param int $customerId
     * @param BillingTypeEnum $billingType
     * @param float $value
     * @param string $description
     * @param DateTimeInterface $dueDate
     */
    public function __construct(
        public string $asaasCustomerId,
        private int $customerId,
        public BillingTypeEnum $billingType,
        public float $value,
        public string $description,
        public DateTimeInterface $dueDate
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'customer'      => $this->asaasCustomerId,
            'billingType'   => $this->billingType->value,
            'value'         => $this->value,
            'description'   => $this->description,
            'dueDate'       => $this->dueDate->format('Y-m-d'),
        ];
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @return string
     */
    public function getAsaasCustomerId(): string
    {
        return $this->asaasCustomerId;
    }
}
