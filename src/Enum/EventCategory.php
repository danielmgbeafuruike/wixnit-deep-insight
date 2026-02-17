<?php

    namespace Wixnit\DeepInsight\Enum;

    enum EventCategory : string
    {
        case ERROR = "error";
        case WARNING = "warning";
        case SECURITY = "security";
        case PERFORMANCE = "performance";
        case INFO = "info";
        case UNKNOWN = "unknown";
    }