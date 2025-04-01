<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\ProcessPaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Customer;
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
            $data = $request->validated();
            $customerData = $data['customer'];
            $customer = Customer::query()->where('cpf_cnpj', $customerData['cpf_cnpj'])->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $customerData['name'],
                    'email' => $customerData['email'],
                    'cpf_cnpj' => $customerData['cpf_cnpj'],
                    'external_id' => $customerData['external_id'] ?? null,
                ]);
            }

            $asaasCustomerId = $this->paymentService->getOrCreateCustomer($customerData, $customer->id);

            if (empty($asaasCustomerId)) {
                throw new Exception('Não foi possível obter ou criar um ID de cliente Asaas válido');
            }

            $paymentDTO = new PaymentDTO(
                $asaasCustomerId,
                $customer->id,
                BillingTypeEnum::from($data['billing_type']),
                $data['value'],
                $data['description'],
                new DateTime($data['due_date'])
            );

            $payment = $this->paymentService->processPayment($paymentDTO);


            return new PaymentResource($payment)->additional([
                'success' => true,
                'message' => 'Pagamento processado com sucesso'
            ]);
        } catch (Exception $e) {
            logger('Erro ao processar pagamento', [
                'data' => $data ?? $request->all(),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar pagamento: ' . $e->getMessage()
            ], 500);
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

