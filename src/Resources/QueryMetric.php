<?php

    namespace Wixnit\DeepInsight\Resources;

    class QueryMetric
    {
        public function __construct(
            public String $query,
            public int $avg_ms,
            public int $count,
        ){}
    }