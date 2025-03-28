<?php

namespace ACTCMS\MoMo\Http\Controllers;

use ACTCMS\MoMo\Http\Requests\MoMoPaymentIPNRequest;
use ACTCMS\Base\Http\Responses\BaseHttpResponse;
use ACTCMS\MoMo\Http\Requests\MoMoPaymentCallbackRequest;
use ACTCMS\MoMo\Services\Gateways\MoMoPaymentService;
use ACTCMS\Payment\Supports\PaymentHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class MoMoController extends Controller
{
    /**
     * Get callback from MoMo
     *
     * @param MoMoPaymentCallbackRequest $request
     * @param MoMoPaymentService $momoPaymentService
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getCallback(
        MoMoPaymentCallbackRequest $request,
        MoMoPaymentService         $momoPaymentService,
        BaseHttpResponse           $response
    )
    {
        $status = $momoPaymentService->getPaymentStatus($request);
        $token = $momoPaymentService->getToken($request->input());

        if ($status === 'error' || $status === 'hacked') {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage(__('Payment failed!'));
        }

        $momoPaymentService->storedData($request->input());

        return $response
            ->setNextUrl(PaymentHelper::getRedirectURL($token))
            ->setMessage(__('Checkout successfully!'));
    }

    /**
     * Get IPN from MoMo
     *
     * @param MoMoPaymentIPNRequest $request
     * @param MoMoPaymentService $momoPaymentService
     * @return JsonResponse
     */
    public function getIPN(
        MoMoPaymentIPNRequest $request,
        MoMoPaymentService    $momoPaymentService
    )
    {
        return response()->json($momoPaymentService->afterPayment($request->input()));
    }
}
