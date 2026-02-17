<?php

    namespace Wixnit\DeepInsight\Traffic;

    class UserIntent
    {
        public function __construct(
            public float $high,
            public float $medium,
            public float $low,
        ){}
    }