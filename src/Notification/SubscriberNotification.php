<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class SubscriberNotification extends BaseNotification
    {
        public function __construct(
            public string $email,
            public string $phone = "",
        )
        {
            $this->title = "New Subscriber";
            $this->content = "A new subscriber with ".(($email != "") ? "email: ".$email : "")." ".(($phone != "") ? "phone: ".$phone : "");
            $this->action = "new_subscriber";
            $this->category = NotificationCategory::NEW_SUBSCRIBER;
            $this->type = NotificationType::ANOUNCEMENT;
        }
    }