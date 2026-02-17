<?php

    namespace Wixnit\DeepInsight\Users;

    use Wixnit\DeepInsight\NameValuePair;
    use Wixnit\Utilities\Date;

    class DeepInsightUser
    {
        public function __construct(
            public string $id,
            public string $name,
            public string $phone,
            public string $email,
            public string $country,
            public string $deviceType,
            public string $appVersion,
            public bool $isSubscribed,
            public bool $isPaying,
            public int $lastActive,
            public int $signupDate,
            public int $sessionCount,

            public string $ip = "",
            public string $osVersion = "",
            public string $detectedCountry = "",

            /**
             * @param ErrorLog[] $error
             */
            public array $errors = [],

            /**
             * @param UserSession[] $recentSession
             */
            public $recentSessions = [],

            /**
             * @param NameValuePair[] $featureUsage
             */
            public $featureUsage = [],
            public int $totalSessions = 0,
            public int $avgSessionMinutes = 0,

            /**
             * @param UserUsageTimeline[] $usageTimeline
             */
            public array $usageTimeline = [],
        ){}
    }