<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\MidtransCallbackRequest;
use App\Http\Requests\Payment\StorePaymentRequest;

use App\Models\Payment;

use App\Services\Payment\PaymentService;



class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }
    public function create(StorePaymentRequest $request, $OrderId)
    {
        $this->authorize('create', Payment::class);
        $user = $request->user();
    

        $payment = $this->paymentService->checkout($user, $OrderId);




        return response()->json($payment);
    }


    public function callback(MidtransCallbackRequest $request)
    {
        $result = $this->paymentService->callback($request->validated());

        return response()->json($result);
    }
}
