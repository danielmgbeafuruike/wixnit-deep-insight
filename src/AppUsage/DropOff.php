<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class DropOff
    {
        public function __construct(
            public string $screen,
            public int $value,
        ){}
    }