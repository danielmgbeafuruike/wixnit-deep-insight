<?php

    namespace Wixnit\DeepInsight\Event;

    use stdClass;
    use Wixnit\App\Model;
    use Wixnit\DeepInsight\Enum\Severity;

    class DeepInsightEventLog extends Model
    {
        public string $title;
        public string $description;
        public string $category;
        public Severity $severity = Severity::UNKNOWN;
        public stdClass $metadata;


        protected array $longText = ["title", "description"];
    }