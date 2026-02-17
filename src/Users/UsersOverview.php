<?php

    namespace Wixnit\DeepInsight\Users;

    use Wixnit\DeepInsight\Metrics\AppVersionMetric;
    use Wixnit\DeepInsight\Metrics\CountryMetric;
    use Wixnit\DeepInsight\Metrics\DeviceMetric;
    use Wixnit\DeepInsight\NameValuePair;

    class UsersOverview
    {
        public bool $isSubcriptionClient = false;
        public bool $isPaymentClient = false;

        function __construct(public int $totalUsers = 0,
            public int $activeUsers = 0, //last 24hrs
            public int $newUsers = 0, //this week
            public int $subscribed = 0, //subcribed users;
            public int $unsubscribed = 0,
            public int $payingUsers = 0, //subcribed users;
            public int $freeUsers = 0,

            /**
             * Summary of devicesDistribution
             * @var DeviceMetric[]
             */
            public array $devicesDistribution = [],

            /**
             * Summary of countriesDistribution
             * @var CountryMetric[]
             */
            public array $countriesDistribution = [],

            /**
             * Summary of citiesDistribution
             * @var AppVersionMetric[]
             */
            public array $appDistribution = [],
            
            /**
             * @param NameValuePair[] $name
             */
            public array $adoption = [],
            ){}


        public function setPaymentClient(bool $isPaymentClient = true): UsersOverview
        {
            $this->isPaymentClient = $isPaymentClient;
            return $this;
        }

        public function setSubscriptionClient(bool $isSubscriptionClient = true): UsersOverview
        {
            $this->isSubcriptionClient = $isSubscriptionClient;
            return $this;
        }
    }