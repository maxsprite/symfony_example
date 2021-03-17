<?php

namespace App\Entity\Constants;

class RequestTransactionStatus
{
    const
        HOLD = 'HOLD',
        CALL_STARTED = 'CALL_STARTED',
        CALL_ENDED = 'CALL_ENDED',
        CALL_DISCONNECTED_NETWORK  = 'CALL_DISCONNECTED_NETWORK',
        REFUND_FOR_CLIENT = 'REFUND_FOR_CLIENT ',
        REFUND_FOR_CLIENT_WITH_TAX = 'REFUND_FOR_CLIENT_WITH_TAX',
        REFUND_FOR_CLIENT_DISCONNECTED = 'REFUND_FOR_CLIENT_DISCONNECTED',
        CLIENT_UNAVAILABLE = 'CLIENT_UNAVAILABLE',
        CLIENT_REJECT = 'CLIENT_REJECT',
        CONSULTANT_UNAVAILABLE = 'CONSULTANT_UNAVAILABLE',
        CONSULTANT_REJECT = 'CONSULTANT_REJECT',
        CONSULTANT_SYSTEM_TAX = 'CONSULTANT_SYSTEM_TAX',
        CONSULTANT_PROFIT = 'CONSULTANT_PROFIT',
        CALL_DISCONNECTED_USERS = 'CALL_DISCONNECTED_USERS'
    ;

    public static function getValues()
    {
        return [
            self::HOLD,
            self::CALL_STARTED,
            self::CALL_ENDED,
            self::CALL_DISCONNECTED_NETWORK,
            self::REFUND_FOR_CLIENT,
            self::REFUND_FOR_CLIENT_WITH_TAX,
            self::REFUND_FOR_CLIENT_DISCONNECTED,
            self::CLIENT_UNAVAILABLE,
            self::CLIENT_REJECT,
            self::CONSULTANT_UNAVAILABLE,
            self::CONSULTANT_REJECT,
            self::CONSULTANT_SYSTEM_TAX,
            self::CONSULTANT_PROFIT,
            self::CALL_DISCONNECTED_USERS,
        ];
    }
}