<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class HeatmapCell
    {
        public function __construct(
            /**
             * the hour of the day. an int value from 0 to 23
             */
            public int $hour,

            /**
             * the day of the week. an int value from 0 to 6, where 0 is Monday and 6 is Sunday
             */
            public int $day,
            public int $count,
        ) {}
    }