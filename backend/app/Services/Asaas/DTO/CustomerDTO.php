<?php

namespace App\Services\Asaas\DTO;

readonly class CustomerDTO
{
    /**
     * @param string $name
     * @param string $email
     * @param string|null $cpfCnpj
     * @param string|null $phone
     * @param string|null $mobilePhone
     * @param string|null $address
     * @param string|null $addressNumber
     * @param string|null $complement
     * @param string|null $province
     * @param string|null $postalCode
     * @param string|null $externalReference
     * @param string|null $municipalInscription
     * @param string|null $stateInscription
     * @param string|null $observations
     * @param string|null $groupName
     * @param string|null $company
     * @param array|null $additionalEmails
     * @param bool|null $notificationDisabled
     */
    public function __construct(
        public string $name,
        public string $email,
        public ?string $cpfCnpj = null,
        public ?string $phone = null,
        public ?string $mobilePhone = null,
        public ?string $address = null,
        public ?string $addressNumber = null,
        public ?string $complement = null,
        public ?string $province = null,
        public ?string $postalCode = null,
        public ?string $externalReference = null,
        public ?string $municipalInscription = null,
        public ?string $stateInscription = null,
        public ?string $observations = null,
        public ?string $groupName = null,
        public ?string $company = null,
        public ?array $additionalEmails = null,
        public ?bool $notificationDisabled = null
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'name'                  => $this->name,
            'email'                 => $this->email,
            'cpfCnpj'               => $this->cpfCnpj,
            'phone'                 => $this->phone,
            'mobilePhone'           => $this->mobilePhone,
            'address'               => $this->address,
            'addressNumber'         => $this->addressNumber,
            'complement'            => $this->complement,
            'province'              => $this->province,
            'postalCode'            => $this->postalCode,
            'externalReference'     => $this->externalReference,
            'notificationDisabled'  => $this->notificationDisabled,
            'additionalEmails'      => $this->additionalEmails,
            'municipalInscription'  => $this->municipalInscription,
            'stateInscription'      => $this->stateInscription,
            'observations'          => $this->observations,
            'groupName'             => $this->groupName,
            'company'               => $this->company,
        ], fn ($value) => $value !== null);
    }
}
