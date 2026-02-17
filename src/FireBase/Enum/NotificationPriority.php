<?php

    namespace Wixnit\DeepInsight\FireBase\Enum;

    enum NotificationPriority: string
    {
        case PRIORITY_UNSPECIFIED = "PRIORITY_UNSPECIFIED"; //If priority is unspecified, notification priority is set to PRIORITY_DEFAULT.
        case PRIORITY_MIN = "PRIORITY_MIN"; //Lowest notification priority. Notifications with this PRIORITY_MIN might not be shown to the user except under special circumstances, such as detailed notification logs.
        case PRIORITY_LOW = "PRIORITY_LOW"; //Lower notification priority. The UI may choose to show the notifications smaller, or at a different position in the list, compared with notifications with PRIORITY_DEFAULT.
        case PRIORITY_DEFAULT = "PRIORITY_DEFAULT"; //Default notification priority. If the application does not prioritize its own notifications, use this value for all notifications.
        case PRIORITY_HIGH = "PRIORITY_HIGH"; //Higher notification priority. Use this for more important notifications or alerts. The UI may choose to show these notifications larger, or at a different position in the notification lists, compared with notifications with PRIORITY_DEFAULT.
        case PRIORITY_MAX = "PRIORITY_MAX"; //Highest notification priority. Use this for the application's most important items that require the user's prompt attention or input
    }