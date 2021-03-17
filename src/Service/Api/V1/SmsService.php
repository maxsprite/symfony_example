<?php

namespace App\Service\Api\V1;

class SmsService
{
    public function send(int $phone, string $message)
    {
        return send_sms($phone, $message, 1, 0, 0, 0, 'HIRE');
    }
}