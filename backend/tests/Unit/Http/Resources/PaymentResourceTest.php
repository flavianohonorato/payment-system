<?php

namespace Tests\Unit\Http\Resources;

use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentResourceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_resource_for_boleto_payment(): void
    {
        $payment = Payment::factory()->create([
            'billing_type'      => BillingTypeEnum::BOLETO,
            'status'            => PaymentStatusEnum::PENDING,
            'bank_slip_url'     => 'https://example.com/boleto',
            'pix_qr_code'       => null,
            'pix_copy_paste'    => null,
        ]);

        $result = new PaymentResource($payment)->toArray(request());

        $this->assertEquals($payment->id, $result['id']);
        $this->assertEquals($payment->uuid, $result['uuid']);
        $this->assertEquals($payment->value, $result['value']);
        $this->assertEquals(PaymentStatusEnum::PENDING->value, $result['status']);
        $this->assertEquals(BillingTypeEnum::BOLETO->value, $result['billing_type']);
        $this->assertEquals('https://example.com/boleto', $result['bank_slip_url']);
        $this->assertNull($result['pix_qr_code']);
        $this->assertNull($result['pix_copy_paste']);
    }

    /**
     * @return void
     */
    public function test_resource_for_pix_payment(): void
    {
        $payment = Payment::factory()->create([
            'billing_type'   => BillingTypeEnum::PIX,
            'status'         => PaymentStatusEnum::PENDING,
            'pix_qr_code'    => 'QR_CODE_DATA',
            'pix_copy_paste' => 'PIX_COPY_PASTE_DATA',
            'bank_slip_url'  => null,
        ]);

        $resource = new PaymentResource($payment);
        $result = $resource->toArray(request());

        $this->assertEquals($payment->id, $result['id']);
        $this->assertEquals($payment->uuid, $result['uuid']);
        $this->assertEquals($payment->value, $result['value']);
        $this->assertEquals(PaymentStatusEnum::PENDING->value, $result['status']);
        $this->assertEquals(BillingTypeEnum::PIX->value, $result['billing_type']);
        $this->assertEquals('QR_CODE_DATA', $result['pix_qr_code']);
        $this->assertEquals('PIX_COPY_PASTE_DATA', $result['pix_copy_paste']);
        $this->assertNull($result['bank_slip_url']);
    }
}
