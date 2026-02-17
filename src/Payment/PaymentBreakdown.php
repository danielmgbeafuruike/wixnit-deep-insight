<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\NameValuePair;

    class PaymentBreakdown
    {
        public function __construct(

            /**
             * @param NameValuePair[] $byMethod
             */
            public array $byMethod = [],

            /**
             * @param NameValuePair[] $byPlatform
             */
            public array $byPlatform = [],

            /**
             * @param NameValuePair[] $byCurrency
             */
            public array $byCurrency = [],
        ){}
    }