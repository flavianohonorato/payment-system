<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Payment;
use App\Services\Asaas\Enums\BillingTypeEnum;
use App\Services\Asaas\Enums\PaymentStatusEnum;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    /**
     * @return array|mixed[]
     */
    public function definition(): array
    {
        $billingType = $this->faker->randomElement(BillingTypeEnum::cases());
        $status = $this->faker->randomElement(PaymentStatusEnum::cases());

        return [
            'uuid'          => Str::uuid()->toString(),
            'customer_id'   => Customer::factory(),
            'asaas_id'      => 'pay_' . Str::random(20),
            'value'         => $this->faker->randomFloat(2, 10, 1000),
            'status'        => $status,
            'billing_type'  => $billingType,
            'due_date'      => $this->faker->dateTimeBetween('now', '+30 days'),
            'invoice_url'   => $this->faker->url(),
            'bank_slip_url' => $billingType === BillingTypeEnum::BOLETO ? $this->faker->url() : null,
            'pix_qr_code'   => $billingType === BillingTypeEnum::PIX ? $this->faker->randomAscii() : null,
            'pix_copy_paste' => $billingType === BillingTypeEnum::PIX ? $this->faker->text(100) : null,
            'asaas_data'    => $this->generateAsaasData($billingType, $status),
            'error_message' => $status === PaymentStatusEnum::RECEIVED ? $this->faker->sentence() : null,
        ];
    }

    /**
     * @param BillingTypeEnum $billingType
     * @param PaymentStatusEnum $status
     * @return array
     */
    protected function generateAsaasData(BillingTypeEnum $billingType, PaymentStatusEnum $status): array
    {
        $data = [
            'id'            => 'pay_' . Str::random(20),
            'customer'      => 'cus_' . Str::random(20),
            'value'         => $this->faker->randomFloat(2, 10, 1000),
            'netValue'      => $this->faker->randomFloat(2, 10, 1000),
            'status'        => $status->value,
            'billingType'   => $billingType->value,
            'dueDate'       => $this->faker->date(),
            'invoiceUrl'    => $this->faker->url(),
            'description'   => $this->faker->sentence(),
        ];

        if ($billingType === BillingTypeEnum::BOLETO) {
            $data['bankSlipUrl'] = $this->faker->url();
        }

        if ($billingType === BillingTypeEnum::PIX) {
            $data['pix'] = [
                'qrCode'            => $this->faker->randomAscii(),
                'payload'           => $this->faker->text(100),
                'expirationDate'    => $this->faker->dateTimeBetween(
                    'now',
                    '+1 hour'
                )->format('Y-m-d\TH:i:s\Z'),
            ];
        }

        if ($billingType === BillingTypeEnum::CREDIT_CARD) {
            $data['creditCard'] = [
                'creditCardNumber'  => $this->faker->creditCardNumber(),
                'creditCardBrand'   => $this->faker->randomElement(['VISA', 'MASTERCARD', 'AMEX']),
                'creditCardToken'   => $this->faker->md5(),
            ];
        }

        return $data;
    }

    /**
     * @return self
     */
    public function pending(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::PENDING,
        ]);
    }

    /**
     * @return self
     */
    public function confirmed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::CONFIRMED,
        ]);
    }

    /**
     * @return self
     */
    public function received(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => PaymentStatusEnum::RECEIVED,
        ]);
    }
}

