<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name'          => $this->name,
            'email'         => $this->email,
            'cpf_cnpj'      => $this->cpf_cnpj,
            'phone'         => $this->phone,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at
        ];
    }
}
