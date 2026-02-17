<?php

    namespace Wixnit\DeepInsight;

    class Stat
    {
        public function __construct(
            public string $title,
            public float $value,
            public float $delta,
        ){}
    }