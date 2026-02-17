<?php

    namespace Wixnit\DeepInsight\FireBase\Enum;

    enum Visibility: string
    {
        case VISIBILITY_UNSPECIFIED = "VISIBILITY_UNSPECIFIED"; //If unspecified, default to Visibility.PRIVATE.
        case PRIVATE = "PRIVATE"; //Show this notification on all lockscreens, but conceal sensitive or private information on secure lockscreens.
        case PUBLIC = "PUBLIC"; //Show this notification in its entirety on all lockscreens.
        case SECRET = "SECRET"; //Do not reveal any part of this notification on a secure lockscreen.
    }