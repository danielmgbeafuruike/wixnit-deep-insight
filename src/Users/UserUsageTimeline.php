<?php

    namespace Wixnit\DeepInsight\Users;

    class UserUsageTimeline
    {
        public function __construct(
            public float $timestamp,
            public float $value,
        ){}
    }