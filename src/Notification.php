<?php

    namespace Wixnit\DeepInsight;

    class Notification
    {
        public string $recipient;
        public string $subject;
        public string $body;

        public function __construct(string $recipient, string $subject, string $body)
        {
            $this->recipient = $recipient;
            $this->subject = $subject;
            $this->body = $body;
        }
    }