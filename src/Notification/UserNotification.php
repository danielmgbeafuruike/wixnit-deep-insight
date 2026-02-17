<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class UserNotification extends BaseNotification
    {
        public function __construct(
            public string $name,
            public string $country,
            public string $deviceType,
            public string $os,
        ){
            $this->title = "New User";
            $this->content = $name." from ".$country." on ".$os." ".$deviceType." just created an account";
            $this->action = "new_user";
            $this->category = NotificationCategory::NEW_USER;
            $this->type = NotificationType::INFO;
        }
    }