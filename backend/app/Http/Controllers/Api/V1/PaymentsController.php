<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Services\Asaas\AsaasPaymentService;
use App\Services\Asaas\DTO\PaymentDTO;
use App\Services\Asaas\Enums\BillingTypeEnum;
use DateTime;
use Exception;
use Illuminate\Http\JsonResponse;

class PaymentsController extends Controller
{
    /**
     * @param AsaasPaymentService $paymentService
     */
    public function __construct(private readonly AsaasPaymentService $paymentService) {}

    /**
     * @param ProcessPaymentRequest $request
     * @return PaymentResource|JsonResponse
     */
    public function process(ProcessPaymentRequest $request): PaymentResource|JsonResponse
    {
        try {
            $asaasCustomerId = $this->paymentService->getOrCreateCustomer(
                $request->validated('customer'),
                $request->validated('customer_id')
            );

            $paymentDTO = new PaymentDTO(
                asaasCustomerId: $asaasCustomerId,
                customerId: $request->validated('customer_id'),
                billingType: BillingTypeEnum::from($request->validated('billing_type')),
                value: (float) $request->validated('value'),
                description: $request->validated('description'),
                dueDate: new DateTime($request->validated('due_date'))
            );

            $payment = $this->paymentService->processPayment($paymentDTO);

            return new PaymentResource($payment);
        } catch (Exception $e) {
            logger('Erro ao processar pagamento', [
                'message' => $e->getMessage(),
                'data' => $request->validated(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @param Payment $payment
     * @return JsonResponse
     */
    public function show(Payment $payment): JsonResponse
    {
        return response()->json(new PaymentResource($payment));
    }
}

