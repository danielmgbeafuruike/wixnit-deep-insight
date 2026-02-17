<?php

    namespace Wixnit\DeepInsight\Endpoint;

    use Wixnit\DeepInsight\NameValuePair;
    use Wixnit\Enum\HTTPMethod;

    class DeepInsightEndpoint
    {
        public function __construct(
            public string $name,
            public HTTPMethod $method, // GET, POST, PUT, DELETE
            public string $path,

            /**
             * @param NameValuePair[] $payload
             */
            public array $payload,
        ){}
    }