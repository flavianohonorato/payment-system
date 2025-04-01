<?php

namespace Tests\Feature\Controllers\Api\V1;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThankYouControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_show_boleto_payment_details(): void
    {
        $customer = Customer::factory()->create();
        $payment = Payment::factory()->create([
            'customer_id'   => $customer->id,
            'billing_type'  => BillingTypeEnum::BOLETO,
            'status'        => PaymentStatusEnum::PENDING,
            'bank_slip_url' => 'https://example.com/boleto',
            'invoice_url'   => 'https://example.com/invoice',
        ]);

        $response = $this->getJson(route('api.thank-you.show', $payment->uuid));

        $response->assertStatus(200)
            ->assertJson([
                'paymentType'   => BillingTypeEnum::BOLETO->value,
                'bankSlipUrl'   => 'https://example.com/boleto',
                'invoiceUrl'    => 'https://example.com/invoice',
                'status'        => PaymentStatusEnum::PENDING->value,
            ])
            ->assertJsonMissing([
                'pixQrCode',
                'pixCopyPaste',
            ]);
    }

    /**
     * @return void
     */
    public function test_show_pix_payment_details(): void
    {
        $customer = Customer::factory()->create();
        $payment = Payment::factory()->create([
            'customer_id'   => $customer->id,
            'billing_type'  => BillingTypeEnum::PIX,
            'status'        => PaymentStatusEnum::PENDING,
            'pix_qr_code'   => 'QR_CODE_DATA',
            'pix_copy_paste' => 'PIX_COPY_PASTE_DATA',
            'invoice_url'   => 'https://example.com/invoice',
        ]);

        $response = $this->getJson(route('api.thank-you.show', $payment->uuid));

        $response->assertStatus(200)
            ->assertJson([
                'paymentType'   => BillingTypeEnum::PIX->value,
                'pixQrCode'     => 'QR_CODE_DATA',
                'pixCopyPaste'  => 'PIX_COPY_PASTE_DATA',
                'invoiceUrl'    => 'https://example.com/invoice',
                'status'        => PaymentStatusEnum::PENDING->value,
            ])
            ->assertJsonMissing([
                'bankSlipUrl',
            ]);
    }

    /**
     * @return void
     */
    public function test_show_payment_not_found(): void
    {
        $response = $this->getJson(route('api.thank-you.show', 'nonexistent-uuid'));

        $response->assertStatus(404);
    }
}
