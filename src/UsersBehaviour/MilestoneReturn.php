<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class MilestoneReturn
    {
        public function __construct(
            public string $milestone,
            public int $completed,
            public int $returned,
        ){}
    }