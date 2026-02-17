<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\Currency;

    class PaymentInsight
    {
        public function __construct(
            public Currency $currency,
            public PaymentSummary $summary,

            /**
             * @param Stat[] $customStats
             */
            public array $customStats,

            /**
             * @param RevenuePoint[] $revenueTrend
             */
            public array $revenueTrend,
            public PaymentBreakdown $breakdown,
            public SubscriptionInsight $subscriptions,
            public SpendingBehavior $behavior,

            /**
             * @param PaymentEvent[] $events
             */
            public array $events,

            /**
             * @param GeoPaymentStat[] $geoStats
             */
            public array $geoStats,
            public FailedPaymentStats $failedStats,

            /**
             * @param TopCustomer[] $topCustomers
             */
            public array $topCustomers,
        ){}
    }