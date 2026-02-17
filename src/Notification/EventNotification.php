<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\EventCategory;
    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class EventNotification extends BaseNotification
    {
        public function __construct(
            public EventCategory $eventCategory,
            public string $description,            
        )
        {
            $this->title = $eventCategory == $eventCategory->name." Event";
            $this->content = $description;
            $this->action = "new_event";
            $this->category = NotificationCategory::NEW_EVENT;
            $this->type = NotificationType::ALERT;
        }
    }