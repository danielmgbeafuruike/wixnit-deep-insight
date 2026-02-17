<?php

    namespace Wixnit\DeepInsight;

    use Wixnit\DeepInsight\Enum\Priority;

    class Event
    {
        public Priority $priority;
        public string $message;
        public array $context;

        public function __construct(Priority $priority, string $message, array $context = [])
        {
            $this->priority = $priority;
            $this->message = $message;
            $this->context = $context;
        }
    }