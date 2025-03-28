<?php

namespace App\Models;

use Database\Factories\AsaasCustomerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsaasCustomer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'asaas_id',
        'date_created',
        'external_reference',
        'notification_disabled',
        'observations',
        'foreign_customer',
        'additional_emails',
        'person_type',
        'deleted',
        'asaas_data',
    ];

    protected $casts = [
        'date_created' => 'date',
        'asaas_data' => 'array',
    ];

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return AsaasCustomerFactory
     */
    protected static function newFactory(): AsaasCustomerFactory
    {
        return AsaasCustomerFactory::new();
    }
}
