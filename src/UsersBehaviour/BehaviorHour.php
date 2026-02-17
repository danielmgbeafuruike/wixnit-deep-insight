<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class BehaviorHour
    {
        public function __construct(
            public int $hour, // 0 - 23
            public int $count,
        ){}
    }