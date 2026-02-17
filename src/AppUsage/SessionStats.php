<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class SessionStats
    {
        public function __construct(
            public float $avgSessionsPerUser,
            public float $avgSessionDuration,

            /**
             * line elements for 23 hours, 0-23
             * @param int[] $hourlyStarts
             */
            public array $hourlyStats =[], // 24 elements for hours 0-23

            /**
             * list of dropoffs
             * @param DropOff[] $dropOffPercentage
             */
            public array $dropOffPercentage =[], // key=screen, value=drop-off %
        ) {}
    }