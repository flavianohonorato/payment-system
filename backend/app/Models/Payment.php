<?php

namespace App\Models;

use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'uuid',
        'customer_id',
        'asaas_id',
        'value',
        'status',
        'billing_type',
        'due_date',
        'invoice_url',
        'bank_slip_url',
        'pix_qr_code',
        'pix_copy_paste',
        'asaas_data',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'asaas_data'    => 'array',
        'metadata'      => 'array',
        'value'         => 'decimal:2',
        'due_date'      => 'date',
        'status'        => PaymentStatusEnum::class,
        'billing_type'  => BillingTypeEnum::class,
    ];

    /**
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Payment $payment) {
            $payment->uuid = $payment->uuid ?? Str::uuid()->toString();
        });
    }

    /**
     * @return PaymentFactory
     */
    protected static function newFactory(): PaymentFactory
    {
        return PaymentFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->status === PaymentStatusEnum::PENDING;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->status === PaymentStatusEnum::CONFIRMED || $this->status === PaymentStatusEnum::RECEIVED;
    }

    /**
     * @return bool
     */
    public function isBilled(): bool
    {
        return $this->billing_type === BillingTypeEnum::BOLETO;
    }

    public function isPix(): bool
    {
        return $this->billing_type === BillingTypeEnum::PIX;
    }
}
