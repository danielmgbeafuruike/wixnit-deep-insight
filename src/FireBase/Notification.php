<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class Notification
    {
        public string $title = ""; //The notification's title.
        public string $body = ""; //The notification's body text.
        public string $image = ""; //Contains the URL of an image that is going to be downloaded on the device and displayed in a notification. JPEG, PNG, BMP have full support across platforms. Animated GIF and video only work on iOS. WebP and HEIF have varying levels of support across platforms and platform versions. Android has 1MB image size limit. Quota usage and implications/costs for hosting image on Firebase Storage: https://firebase.google.com/pricing
    
        public static function from(string $title, string $body)
        {
            $ret = new Notification();
            $ret->title = $title;
            $ret->body = $body;

            return $ret;
        }

        public function withImage(string $image): Notification
        {
            $this->image = $image;
            return $this;
        }

        public function getObject(): ?stdClass
        {
            if(($this->title != null) || ($this->body != null) || ($this->image != null))
            {
                $ret = new stdClass();
                
                if($this->title != null)
                {
                    $ret->title = $this->title;
                }
                if($this->body != null)
                {
                    $ret->body = $this->body;
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