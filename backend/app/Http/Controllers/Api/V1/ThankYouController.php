<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;

class ThankYouController extends Controller
{
    /**
     * @param string $paymentId
     * @return JsonResponse
     */
    public function show(string $paymentId): JsonResponse
    {
        $payment = Payment::query()->where('uuid', $paymentId)->firstOrFail();

        $paymentData = [
            'paymentType'   => $payment->billing_type,
            'invoiceUrl'    => $payment->invoice_url,
            'status'        => $payment->status,
            'errorMessage'  => $payment->error_message,
        ];

        if ($payment->isPix()) {
            $paymentData['pixQrCode']    = $payment->pix_qr_code;
            $paymentData['pixCopyPaste'] = $payment->pix_copy_paste;
        }

        if ($payment->isBilled()) {
            $paymentData['bankSlipUrl'] = $payment->bank_slip_url;
        }

        return response()->json($paymentData);
    }
}
