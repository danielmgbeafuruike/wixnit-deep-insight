<?php

    namespace Wixnit\DeepInsight\FireBase;

    class Color
    {
        public int $red = 0; //The amount of red in the color as a value in the interval [0, 1].
        public int $green = 0; //The amount of green in the color as a value in the interval [0, 1].
        public int $blue = 0; //The amount of blue in the color as a value in the interval [0, 1].
        public int $alpha = 0; //The fraction of this color that should be applied to the pixel. That is, the final pixel color is defined by the equation:

        /**
         * create color from rgb values
         */
        public static function From(int $red, int $green, int $blue, int $alpha=0): color
        {
            $ret = new Color();
            $ret->red = $red;
            $ret->green = $green;
            $ret->blue = $blue;

            return $ret;
        }


        public function toHex(): string
        {
            return "";
        }
    }