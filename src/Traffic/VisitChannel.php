<?php

    namespace Wixnit\DeepInsight\Traffic;

    class VisitChannel
    {
        public function __construct(
            public int $organic,
            public int $searchEngines,
            public int $aiPlatform,
            public int $referral,
            public int $others,
        ){}
    }