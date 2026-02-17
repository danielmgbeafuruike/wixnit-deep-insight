<?php

    namespace Wixnit\DeepInsight\Task;

    use Wixnit\DeepInsight\NameValuePair;
    use Wixnit\Enum\HTTPMethod;

    class DeepInsightTask
    {
        public function __construct(
            public string $name,
            public string $description,
            public HTTPMethod $method, // GET, POST, PUT, DELETE
            public string $endpoint,

            /**
             * @param NameValuePair[] payload
             */
            public array $payload,
        ){}
    }