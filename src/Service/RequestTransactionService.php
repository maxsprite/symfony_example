<?php

namespace App\Service;

use App\Entity\Constants\RequestTransactionStatus;
use App\Entity\RequestCall;
use App\Entity\RequestTransaction;
use Doctrine\ORM\EntityManagerInterface;

class RequestTransactionService
{
    public function createHoldTransaction(RequestCall $requestCall, EntityManagerInterface $entityManager)
    {
        $requestTransaction = RequestTransaction::init([
            'requestCall' => $requestCall,
            'status' => RequestTransactionStatus::HOLD,
        ]);

        $entityManager->persist($requestTransaction);
        $entityManager->flush();
    }
}