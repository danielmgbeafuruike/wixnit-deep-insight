<?php

    namespace Wixnit\DeepInsight\Payment;

    class SubscriptionInsight
    {
        public function __construct(
            public int $newSubscribers,
            public int $renewals,
            public int $cancellations,
            public float $churnRate,
            public float $mrr,
            public float $expansionMrr,
            public float $churnedMrr,
        ){}
    }