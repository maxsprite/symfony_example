<?php

namespace App\Service;

use App\Entity\RequestCall;

class RequestCallService
{
    public function onHold(RequestCall $requestCall, RequestTransactionService $requestTransactionService)
    {
        $requestTransactionService->createHoldTransaction($requestCall);
    }
}