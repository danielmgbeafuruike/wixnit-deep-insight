<?php

    namespace Wixnit\DeepInsight\Traffic;

    use Wixnit\App\Model;

    class FrontEndTrafficLog extends Model
    {
        public string $page;
        public int $duration;
        public int $scrollDepth;
        public int $scrollDepthPercentage;
        public string $sessionId;
        public string $navIndex; //will be zero if it's the first page to have the session id meaning it's the users landing page
    }