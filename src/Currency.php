<?php

    namespace Wixnit\DeepInsight;

    class Currency
    {
        public function __construct(
            public string $symbol,
            public string $code = "",
            public string $name = "",
            public float $value = 0,
            public string $country = "",
            public string $countryCode = "",
        ){}
    }