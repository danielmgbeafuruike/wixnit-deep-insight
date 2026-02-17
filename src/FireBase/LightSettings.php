<?php

    /**
     * For controlling notification on delivery
     */
    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class LightSettings
    {
        public ?Color $color = null; //Required. Set color of the LED with google.type.Color.
        public ?string $light_on_duration = null; //Required. Along with light_off_duration, define the blink rate of LED flashes. (A duration in seconds with up to nine fractional digits, ending with 's'. Example: "3.5s")
        public ?string $light_off_duration = null; //Required. Along with light_on_duration, define the blink rate of LED flashes. (A duration in seconds with up to nine fractional digits, ending with 's'. Example: "3.5s")

        public static function From(Color $color, string $light_on_duration, string $light_off_duration): LightSettings
        {
            $ret = new LightSettings();
            $ret->color = $color;
            $ret->light_on_duration = $light_on_duration;
            $ret->light_off_duration = $light_off_duration;

            return $ret;
        }


        public function getObject(): ?stdClass
        {
            if(($this->color != null) || ($this->light_on_duration != null) || ($this->light_off_duration != null))
            {
                $ret = new stdClass();
                
                if($this->color != null)
                {
                    $ret->color = $this->color->toHex();
                }
                if($this->light_on_duration != null)
                {
                    $ret->light_on_duration = $this->light_on_duration;
                }
                if($this->light_off_duration != null)
                {
                    $ret->light_off_duration = $this->light_off_duration;
                }
                return $ret;
            }
            return null;
        }
    }

