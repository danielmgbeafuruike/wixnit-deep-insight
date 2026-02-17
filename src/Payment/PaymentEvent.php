<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\Enum\PaymentStatus;

    class PaymentEvent
    {
        public function __construct(
            public string $id,
            public string $userName,
            public float $amount,
            public string $method,
            public PaymentStatus $status, // success, failed, refund, chargeback
            public int $timeStamp,
        ){}
    }