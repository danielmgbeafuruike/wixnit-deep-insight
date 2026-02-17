<?php

    namespace Wixnit\DeepInsight\Enum;

    enum PaymentStatus : string
    {
        case SUCCESS = "success";
        case FAILED = "failed"; 
        case REFUND = "refund"; 
        case CHARGEBACK = "chargeback"; 
        case PENDING = "pending";
        case REVERSES = "reveresed";
        case ABANDONED = "abandoned";
        case ERROR = "error";
        case RENEWED = "renewed";
    }