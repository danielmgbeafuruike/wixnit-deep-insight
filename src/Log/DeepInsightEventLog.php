<?php

    namespace Wixnit\DeepInsight\Log;

    use Wixnit\App\Model;
    use Wixnit\DeepInsight\Enum\EventCategory;
    use Wixnit\DeepInsight\Enum\Severity;
    use Wixnit\DeepInsight\NameValuePair;

    class DeepInsightEventLog extends Model
    {
        public string $title;
        public string $description;
        public EventCategory $category = EventCategory::UNKNOWN;
        public Severity $severity = Severity::UNKNOWN;

        /**
         * @param NameValuePair[] metadata
         */
        public array $metadata = [];


        protected array $propertyTypes = [
            "metadata"=> NameValuePair::class,
        ];
    }