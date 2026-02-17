<?php

    namespace Wixnit\DeepInsight;

    class NameValuePair
    {
        public function __construct(
            public string $name = "",
            public mixed $value = null,
        ){}
    }