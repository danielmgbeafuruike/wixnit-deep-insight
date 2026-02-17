<?php

    namespace Wixnit\DeepInsight\Users;

    class UserSession
    {
        public function __construct(
            public int $durationMinutes,
            public int $startedAt,
        ){}
    }