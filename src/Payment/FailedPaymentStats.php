<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\NameValuePair;

    class FailedPaymentStats
    {
        public function __construct(
            public float $failureRate,

            /**
             * @param NameValuePair[] $reasons
             */
            public array $reasons = [],

            /**
             * @param NameValuePair[] $byPlatform
             */
            public array $byPlatform = [],
        ){}
    }