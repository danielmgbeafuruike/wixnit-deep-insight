<?php

    namespace Wixnit\DeepInsight\Messaging;

    use Wixnit\App\Model;

    class DeepInsightMessage extends Model
    {
        public String $name;
        public String $phone;
        public String $email;
        public String $subject;
        public String $content;
        public bool $isRead;


        protected array $longText = ["content", "subject"];
    }