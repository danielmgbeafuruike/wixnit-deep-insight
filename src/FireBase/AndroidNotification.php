<?php

    namespace Wixnit\DeepInsight\FireBase;

    use Wixnit\DeepInsight\FireBase\Enum\MessagePriority;
    use Wixnit\DeepInsight\FireBase\Enum\Visibility;
    use stdClass;

    class AndroidNotification
    {
        public ?string $title = null; //The notification's title. If present, it will override google.firebase.fcm.v1.Notification.title.
        public ?string $body = null; //The notification's body text. If present, it will override google.firebase.fcm.v1.Notification.body.
        public ?string $icon = null; //The notification's icon. Sets the notification icon to myicon for drawable resource myicon. If you don't send this key in the request, FCM displays the launcher icon specified in your app manifest.
        public ?string $color = null; //The notification's icon color, expressed in #rrggbb format.
        public ?string $sound = null; //The sound to play when the device receives the notification. Supports "default" or the filename of a sound resource bundled in the app. Sound files must reside in /res/raw/.
        public ?string $tag = null; //Identifier used to replace existing notifications in the notification drawer. If not specified, each request creates a new notification. If specified and a notification with the same tag is already being shown, the new notification replaces the existing one in the notification drawer.
        public ?string $click_action = null; //The action associated with a user click on the notification. If specified, an activity with a matching intent filter is launched when a user clicks on the notification.
        public ?string $body_loc_key = null; //The key to the body string in the app's string resources to use to localize the body text to the user's current localization. See String Resources for more information.
        public array $body_loc_args = []; //Variable string values to be used in place of the format specifiers in body_loc_key to use to localize the body text to the user's current localization. See Formatting and Styling for more information.
        public ?string $title_loc_key = null; //The key to the title string in the app's string resources to use to localize the title text to the user's current localization. See String Resources for more information.
        public array $title_loc_args = []; //Variable string values to be used in place of the format specifiers in body_loc_key to use to localize the body text to the user's current localization. See Formatting and Styling for more information.
        public ?string $channel_id = null; //The notification's channel id (new in Android O). The app must create a channel with this channel ID before any notification with this channel ID is received. If you don't send this channel ID in the request, or if the channel ID provided has not yet been created by the app, FCM uses the channel ID specified in the app manifest.
        public ?string $ticker = null; //Sets the "ticker" text, which is sent to accessibility services. Prior to API level 21 (Lollipop), sets the text that is displayed in the status bar when the notification first arrives.
        public ?bool $sticky = null; //When set to false or unset, the notification is automatically dismissed when the user clicks it in the panel. When set to true, the notification persists even when the user clicks it.
        public ?string $event_time = null; //Set the time that the event in the notification occurred. Notifications in the panel are sorted by this time. A point in time is represented using protobuf.Timestamp. A timestamp in RFC3339 UTC "Zulu" format, with nanosecond resolution and up to nine fractional digits. Examples: "2014-10-02T15:01:23Z" and "2014-10-02T15:01:23.045123456Z".
        public ?bool $local_only = null; //Set whether or not this notification is relevant only to the current device. Some notifications can be bridged to other devices for remote display, such as a Wear OS watch. This hint can be set to recommend this notification not be bridged. See Wear OS guides
        public ?MessagePriority $notification_priority = null; //Set the relative priority for this notification. Priority is an indication of how much of the user's attention should be consumed by this notification. Low-priority notifications may be hidden from the user in certain situations, while the user might be interrupted for a higher-priority notification. The effect of setting the same priorities may differ slightly on different platforms. Note this priority differs from AndroidMessagePriority. This priority is processed by the client after the message has been delivered, whereas AndroidMessagePriority is an FCM concept that controls when the message is delivered.
        public ?bool $default_sound = null; //If set to true, use the Android framework's default sound for the notification. Default values are specified in config.xml.
        public ?bool $default_vibrate_timings = null; //If set to true, use the Android framework's default vibrate pattern for the notification. Default values are specified in config.xml. If default_vibrate_timings is set to true and vibrate_timings is also set, the default value is used instead of the user-specified vibrate_timings.
        public ?bool $default_light_settings = null; //If set to true, use the Android framework's default LED light settings for the notification. Default values are specified in config.xml. If default_light_settings is set to true and light_settings is also set, the user-specified light_settings is used instead of the default value.
        public array $vibrate_timings = []; //Set the vibration pattern to use. Pass in an array of protobuf.Duration to turn on or off the vibrator. The first value indicates the Duration to wait before turning the vibrator on. The next value indicates the Duration to keep the vibrator on. Subsequent values alternate between Duration to turn the vibrator off and to turn the vibrator on. If vibrate_timings is set and default_vibrate_timings is set to true, the default value is used instead of the user-specified vibrate_timings. A duration in seconds with up to nine fractional digits, ending with 's'. Example: "3.5s".
        public ?Visibility $visibility = null; //Set the Notification.visibility of the notification.
        public ?int $notification_count = null; //Sets the number of items this notification represents. May be displayed as a badge count for launchers that support badging.See Notification Badge. For example, this might be useful if you're using just one notification to represent multiple new messages but you want the count here to represent the number of total new messages. If zero or unspecified, systems that support badging use the default, which is to increment a number displayed on the long-press menu each time a new notification arrives.
        public ?LightSettings $light_settings = null; //Settings to control the notification's LED blinking rate and color if LED is available on the device. The total blinking time is controlled by the OS.
        public ?string $image = null; //Contains the URL of an image that is going to be displayed in a notification. If present, it will override google.firebase.fcm.v1.Notification.image.


        public function getObject(): ?stdClass
        {
            if(($this->title != null) || ($this->body != null) || ($this->icon != null) || ($this->color != null) || ($this->sound != null)
                || ($this->tag != null) || ($this->click_action != null) || ($this->body_loc_args != null) || ($this->body_loc_key !=null)
                || ($this->title_loc_args != null) || ($this->title_loc_key != null) || ($this->channel_id != null) || ($this->ticker != null)
                || ($this->sticky != null) || ($this->event_time != null) || ($this->local_only != null) || ($this->notification_priority != null)
                || ($this->default_sound != null) || ($this->default_vibrate_timings != null) || ($this->default_light_settings != null)
                || ($this->vibrate_timings != null) || ($this->visibility != null) || ($this->notification_count != null) || ($this->image != null)
                || (($this->light_settings != null) && ($this->light_settings->getObject() != null)))
            {
                $ret = new stdClass();

                if(($this->light_settings != null) && ($this->light_settings->getObject() != null))
                {
                    $ret->light_settings = $this->light_settings->getObject();
                }

                if($this->title != null)
                {
                    $ret->title = $this->title;
                }
                if($this->color != null)
                {
                    $ret->color = $this->color;
                }
                if($this->icon != null)
                {
                    $ret->icon = $this->icon;
                }
                if($this->sound != null)
                {
                    $ret->sound = $this->sound;
                }
                if($this->tag != null)
                {
                    $ret->tag = $this->tag;
                }
                if($this->click_action != null)
                {
                    $ret->click_action = $this->click_action;
                }
                if($this->body_loc_args != null)
                {
                    $ret->body_loc_args = $this->body_loc_args;
                }
                if($this->body_loc_key != null)
                {
                    $ret->body_loc_key = $this->body_loc_key;
                }
                if($this->title_loc_args != null)
                {
                    $ret->title_loc_args = $this->title_loc_args;
                }
                if($this->channel_id != null)
                {
                    $ret->channel_id = $this->channel_id;
                }
                if($this->ticker != null)
                {
                    $ret->ticker = $this->ticker;
                }
                if($this->sticky != null)
                {
                    $ret->sticky = $this->sticky;
                }
                if($this->event_time != null)
                {
                    $ret->event_time = $this->event_time;
                }
                if($this->local_only != null)
                {
                    $ret->local_only = $this->local_only;
                }
                if($this->notification_priority != null)
                {
                    $ret->notification_priority = $this->notification_priority;
                }
                if($this->default_vibrate_timings != null)
                {
                    $ret->default_vibrate_timings = $this->default_vibrate_timings;
                }
                if($this->default_light_settings != null)
                {
                    $ret->default_light_settings = $this->default_light_settings;
                }
                if($this->vibrate_timings != null)
                {
                    $ret->vibrate_timings = $this->vibrate_timings;
                }
                if($this->visibility != null)
                {
                    $ret->visibility = $this->visibility;
                }
                if($this->notification_count != null)
                {
                    $ret->notification_count = $this->notification_count;
                }
                if($this->image != null)
                {
                    $ret->image = $this->image;
                }
                if($this->title_loc_key != null)
                {
                    $ret->title_loc_key = $this->title_loc_key;
                }

                return $ret;
            }
            return null;
        }
    }