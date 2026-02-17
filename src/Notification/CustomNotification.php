<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class CustomNotification extends BaseNotification
    {
        public function __construct(
            public string $title,
            public string $content,
            public string $action,
            public NotificationCategory $category,
            public NotificationType $type,
        )
        {
            $this->title = $title;
            $this->content = $content;
            $this->action = $action;
            $this->category = $category;
            $this->type = $type;
        }
    }