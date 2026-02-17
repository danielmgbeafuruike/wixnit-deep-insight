<?php

    namespace Wixnit\DeepInsight\Payment;

    class GeoPaymentStat
    {
        public function __construct(
            public string $country,
            public int $payingUsers,
            public float $totalRevenue,
        ){}
    }