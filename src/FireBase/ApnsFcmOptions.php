<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class ApnsFcmOptions
    {
        public ?string $analytics_label = null; //Label associated with the message's analytics data.
        public ?string $image = null; //Contains the URL of an image that is going to be displayed in a notification. If present, it will override google.firebase.fcm.v1.Notification.image.

        function __construct(string $analytics_label, string $image)
        {
            $this->analytics_label = $analytics_label;
            $this->image = $image;
        }

        public function getObject(): ?stdClass
        {
            if(($this->analytics_label != null) || ($this->image != null))
            {
                $ret = new stdClass();
                
                if($this->analytics_label != null)
                {
                    $ret->analytics_label = $this->analytics_label;
                }
                if($this->image != null)
                {
                    $ret->image = $this->image;
                }
                return $ret;
            }
            return null;
        }
    }