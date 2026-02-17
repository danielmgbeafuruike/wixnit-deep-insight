<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class CohortRetention
    {
        public function __construct(
            public string $cohortName,

            /**
             * @param float[] $weeklyRetention
             */
            public array $weeklyRetention, 
        ){}
    }