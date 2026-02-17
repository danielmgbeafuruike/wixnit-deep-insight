<?php

    namespace Wixnit\DeepInsight\Payment;

    class RevenuePoint
    {
        public function __construct(
            public int $timestamp,
            public float $amount,
        ){}
    }