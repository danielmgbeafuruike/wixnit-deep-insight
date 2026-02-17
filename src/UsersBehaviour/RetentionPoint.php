<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class RetentionPoint
    {
        public function __construct(
            public int $day,
            public float $percentage,
        ){}
    }