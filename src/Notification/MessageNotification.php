<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class MessageNotification extends BaseNotification
    {
        public function __construct(
            public string $name,
            public string $email,
            public string $phone,
        )
        {
            $this->title = "New Message";
            $this->content = "New message from ".$name." phone: ".$phone." and email ".$email;
            $this->action = "new_message";
            $this->category = NotificationCategory::NEW_MESSAGE;
            $this->type = NotificationType::INFO;
        }
    }