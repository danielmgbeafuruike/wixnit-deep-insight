<?php

    namespace Wixnit\DeepInsight\FireBase;

    class AndroidFcmOptions
    {
        public string $analytics_label; //Label associated with the message's analytics data.

        function __construct(string $analytics_label)
        {
            $this->analytics_label = $analytics_label;
        }
    }