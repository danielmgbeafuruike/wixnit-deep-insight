<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\App\Model;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class DeepInsightNotification extends Model
    {
        public NotificationType $type = NotificationType::UNKNOWN;
        public string $title;
        public string $content;
        public bool $isRead;


        protected array $longText = ["content", "title"];
    }