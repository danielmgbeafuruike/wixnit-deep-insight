<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class DeviceUsage
    {
        public function __construct(
            /**
             * this is the device type like iOS, Android, Web
             */
            public string $platform,
            public int $users,
        ){}
    }