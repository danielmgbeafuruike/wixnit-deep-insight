<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class WebpushFcmOptions
    {
        public ?string $link = null;
        public ?string $analytics_label = null;

        function __construct(string $link, string $analytics_label)
        {
            $this->link = $link;
            $this->analytics_label = $analytics_label;
        }


        public function getObject(): ?stdClass
        {
            if(($this->link != null) || ($this->analytics_label != null))
            {
                $ret = new stdClass();

                if($this->link != null)
                {
                    $ret->link = $this->link;
                }
                if($this->analytics_label != null)
                {
                    $ret->analytics_label = $this->analytics_label;
                }

                return $ret;
            }
            return null;
        }
    }