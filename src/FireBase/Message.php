<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class Message
    {
        public ?string $name = null; //Output Only. The identifier of the message sent, in the format of projects/*/messages/{message_id}.
        public ?stdClass $data = null; //Input only. Arbitrary key/value payload, which must be UTF-8 encoded. The key should not be a reserved word ("from", "message_type", or any word starting with "google" or "gcm"). When sending payloads containing only data fields to iOS devices, only normal priority ("apns-priority": "5") is allowed in ApnsConfig. An object containing a list of "key": value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
        public ?Notification $notification = null; //Input only. Basic notification template to use across all platforms.
        public ?AndroidConfig $android = null; //Input only. Android specific options for messages sent through FCM connection server.
        public ?WebpushConfig $webpush = null; //Input only. Webpush protocol options.
        public ?ApnsConfig $apns = null; //Input only. Apple Push Notification Service specific options.
        public ?FCMOptions $fcm_options = null; //Input only. Template for FCM SDK feature options to use across all platforms.

        // Union field target can be only one of the following:
        protected ?string $token = null;
        protected ?string $topic = null;
        protected ?string $condition = null;
        // End of list of possible types for union field target.


        public function toObject(): stdClass
        {
            $ret = new stdClass();

            if($this->name != null)
            {
                $ret->name = $this->name;
            }

            if($this->token != null)
            {
                $ret->token = $this->token;
            }
            if($this->topic != null)
            {
                $ret->topic = $this->topic;
            }
            if($this->condition != null)
            {
                $ret->condition = $this->condition;
            }

            if($this->data != null)
            {
                $ret->data = $this->data;
            }


            if(($this->notification != null) && ($this->notification->getObject() != null))
            {
                $ret->notification = $this->notification->getObject();
            }
            if(($this->android != null) && ($this->android->getObject() != null))
            {
                $ret->android = $this->android->getObject();
            }
            if(($this->webpush != null) && ($this->webpush->getObject()))
            {
                $ret->webpush = $this->webpush->getObject();
            }
            if(($this->apns != null) && ($this->apns->getObject()))
            {
                $ret->apns = $this->apns->getObject();
            }
            if(($this->fcm_options != null) && ($this->fcm_options->getObject()))
            {
                $ret->fcm_options = $this->fcm_options->getObject();
            }
            return $ret;
        }

        public static function toTopic(string $topic): Message
        {
            $ret = new Message();
            $ret->topic = $topic;
            return $ret;
        }

        public static function byConditon(string $condition): Message
        {
            $ret = new Message();
            $ret->condition = $condition;
            return $ret;
        }

        public static function toUser(string $token): Message
        {
            $ret = new Message();
            $ret->token = $token;
            return $ret;
        }
    }