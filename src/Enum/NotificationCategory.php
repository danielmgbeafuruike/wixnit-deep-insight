<?php

    namespace Wixnit\DeepInsight\Enum;

    enum NotificationCategory : string
    {
        case PAYMENT_RECEIVED = "payment.received";
        case RENEWAL_SUCCESSFULL = "renewal.successfull";
        case PAYMENT_FAILED = "payment.failed";
        case RENEWAL_FAILED = "renewal.failed";
        case NEW_USER = "new.user";
        case NEW_MESSAGE = "new.message";
        case NEW_SUBSCRIBER = "new.subscriber";
        case NEW_EVENT = "new.event";
        case CUSTOM_NOTIFICATION = "custom.notification";
    }