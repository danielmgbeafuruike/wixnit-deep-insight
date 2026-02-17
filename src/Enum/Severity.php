<?php

    namespace Wixnit\DeepInsight\Enum;

    enum Severity : string
    {
        case CRITICAL = "critical";
        case ERROR = "error";
        case WARNING = "warning";
        case INFO = "info";
        case UNKNOWN = "unknown";
    }