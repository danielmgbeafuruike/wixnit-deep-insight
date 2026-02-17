<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class BaseNotification
    {
        public string $content;
        public string $title;
        public NotificationCategory $category;
        public NotificationType $type = NotificationType::INFO;
        public string $action;
    }