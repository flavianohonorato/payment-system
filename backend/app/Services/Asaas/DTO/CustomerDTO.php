<?php

namespace App\Services\Asaas\DTO;

readonly class CustomerDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string $cpfCnpj
     * @param string|null $phone
     * @param string|null $address
     * @param int|null $customerId
     */
    public function __construct(
        public string $name,
        public string $email,
        public string $cpfCnpj,
        public ?string $phone = null,
        public ?string $address = null,
        public ?int $customerId = null
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name'      => $this->name,
            'email'     => $this->email,
            'cpfCnpj'   => $this->cpfCnpj,
            'phone'     => $this->phone,
            'address'   => $this->address,
        ];
    }
}
