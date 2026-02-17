<?php

    namespace Wixnit\DeepInsight\Log;

    use Wixnit\App\Model;
    use Wixnit\Enum\HTTPResponseCode;

    class DeepInsightAPILog extends Model
    {
        public string $method;
        public string $path;
        public HTTPResponseCode $status;
        public int $duration;
    }