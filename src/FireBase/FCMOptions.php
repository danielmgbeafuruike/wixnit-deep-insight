<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class FCMOptions
    {
        public ?string $analytics_label = null; //Label associated with the message's analytics data.

        function __construct(string $analytics_label)
        {
            $this->analytics_label = $analytics_label;
        }

        public function getObject(): stdClass
        {
            if($this->analytics_label != null)
            {
                $ret = new stdClass();
                $ret->analytics_label = $this->analytics_label;

                return $ret;
            }
            return null;
        }
    }