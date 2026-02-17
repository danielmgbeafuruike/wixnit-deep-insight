<?php

    namespace Wixnit\DeepInsight\Task;

    class DeepInsightCronJob
    {
        public function __construct(
            public string $name,
            public string $cronExpression,
            public string $endpoint,
            
            public int $waitDuration,
        ){}
    }