<?php

namespace App\Http\Resources;

use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        $status = $this->status instanceof PaymentStatusEnum
            ? (string) $this->status->value
            : (string) $this->status;

        $statusName = $this->status instanceof PaymentStatusEnum
            ? $this->status->name
            : PaymentStatusEnum::tryFrom($this->status)?->name ?? $this->status;

        $billingType = $this->billing_type instanceof BillingTypeEnum
            ? (string) $this->billing_type->value
            : (string) $this->billing_type;

        $billingTypeName = $this->billing_type instanceof BillingTypeEnum
            ? $this->billing_type->name
            : BillingTypeEnum::tryFrom($this->billing_type)?->name ?? $this->billing_type;

        return [
            'id'                => $this->id,
            'uuid'              => $this->uuid,
            'customer_id'       => $this->customer_id,
            'asaas_id'          => $this->asaas_id,
            'value'             => (float) $this->value,
            'status'            => $status,
            'status_name'       => $statusName,
            'billing_type'      => $billingType,
            'billing_type_name' => $billingTypeName,
            'description'       => $this->description,
            'due_date'          => $this->due_date?->format('Y-m-d'),
            'invoice_url'       => $this->invoice_url ?? null,
            'bank_slip_url'     => $this->bank_slip_url ?? null,
            'pix_qr_code'       => $this->pix_qr_code ?? null,
            'pix_copy_paste'    => $this->pix_copy_paste ?? null,
            'created_at'        => $this->created_at?->toISOString(),
            'updated_at'        => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * @param Request $request
     * @return array[]
     */
    public function with(Request $request): array
    {
        return [
            'meta' => [
                'includes_null_fields' => true
            ]
        ];
    }
}
