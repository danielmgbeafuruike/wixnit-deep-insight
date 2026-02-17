<?php

    namespace Wixnit\DeepInsight\Payment;

    class SpendingBehavior
    {
        public function __construct(
            public float $avgPurchasesPerUser,
            public float $repeatPurchaseRate,
            public float $avgTimeBetweenPurchases,

            /**
             * @param float[] $purchaseAmountDistribution
             */
            public array $purchaseAmountDistribution = [], // histogram bins
        ){}
    }