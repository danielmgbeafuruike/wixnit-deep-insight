<?php

    namespace Wixnit\DeepInsight\Enum;

    enum InfoType : string
    {
        case LEVEL_INFO = 'info';
        case LEVEL_WARNING = 'warning';
        case LEVEL_ERROR = 'error';
    }