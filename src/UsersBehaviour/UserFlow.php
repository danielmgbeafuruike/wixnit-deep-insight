<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class UserFlow
    {
        public function __construct(
            public array $steps,
            public int $count,
            public float $avgTime, // minutes
        ){}
    }