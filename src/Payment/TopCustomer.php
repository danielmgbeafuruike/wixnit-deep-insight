<?php

    namespace Wixnit\DeepInsight\Payment;

    class TopCustomer
    {
        public function __construct(
            public string $userId,
            public string $name,
            public string $country,
            public float $totalSpent,
            public int $lastPayment,
        ){}
    }