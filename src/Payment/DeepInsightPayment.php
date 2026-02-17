<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\Currency;
    use Wixnit\DeepInsight\Enum\PaymentStatus;

    class DeepInsightPayment
    {
        public function __construct(
            public string $id,
            public float $amount,
            public string $customerName,
            public PaymentStatus $status,
            public int $date,
            public Currency $currency,
            public string $country,
            public string $method,
            public string $reference,
            public bool $refunded,
            public string $customerId,
            public string $email,
            public string $device,
            public string $platform,
            public string $ip,
        ){}
    }