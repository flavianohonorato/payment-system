<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    use HasFactory;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'email',
        'cpf_cnpj',
        'phone',
        'address',
        'external_id'
    ];

    /**
     * @return HasOne
     */
    public function asaasCustomer(): HasOne
    {
        return $this->hasOne(AsaasCustomer::class);
    }

    /**
     * @return bool
     */
    public function isRegisteredOnAsaas(): bool
    {
        return $this->asaasCustomer()->exists();
    }

    public function getAsaasId()
    {
        return $this->asaasCustomer->asaas_id ?? null;
    }

    /**
     * @return Factory|CustomerFactory
     */
    protected static function newFactory(): Factory|CustomerFactory
    {
        return CustomerFactory::new();
    }
}
