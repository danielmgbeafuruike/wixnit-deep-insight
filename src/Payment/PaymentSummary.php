<?php

    namespace Wixnit\DeepInsight\Payment;

    use Wixnit\DeepInsight\Stat;

    class PaymentSummary
    {
        public function __construct(
            public Stat $totalRevenue,
            public Stat $netProfit,
            public Stat $mrr,
            public Stat $totalTransactions,
            public Stat $arpu,
            public Stat $conversionRate,
            public Stat $refundRate,
        ){}
    }