<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class SessionPeriod
    {
        public function __construct(
            public String $period, // Morning, Afternoon, Evening, Night
            public int $count,
        ){}
    }