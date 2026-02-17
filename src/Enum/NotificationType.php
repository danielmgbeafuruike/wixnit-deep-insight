<?php

    namespace Wixnit\DeepInsight\Enum;

    enum NotificationType : string
    {
        case ANOUNCEMENT = "announcement";
        case ALERT = "alert";
        case UPDATE = "update";
        case REMINDER = "reminder";
        case INFO = "info";
        case UNKNOWN = "unknown";
    }