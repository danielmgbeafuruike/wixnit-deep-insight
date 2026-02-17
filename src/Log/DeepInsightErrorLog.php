<?php

    namespace Wixnit\DeepInsight\Log;

    use Wixnit\App\Model;
    use Wixnit\DeepInsight\Enum\Severity;
    use Wixnit\DeepInsight\NameValuePair;

    class DeepInsightErrorLog extends Model
    {
        public Severity $severity = Severity::UNKNOWN;
        public string $title;
        public string $sourceFile;
        public string $stackTrace;

        /**
         * @param NameValuePair[] $metadata
         */
        public array $metadata = [];
        public bool $isResolved;


        protected array $propertyTypes = [
            "metadata"=> NameValuePair::class,
        ];
    }