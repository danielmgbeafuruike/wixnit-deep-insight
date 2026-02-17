<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class WebpushConfig
    {
        public ?stdClass $headers = null; //map (key: string, value: string) HTTP headers defined in webpush protocol. Refer to Webpush protocol for supported headers, e.g. "TTL": "15". An object containing a list of "key": value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
        public ?stdClass $data = null; //map (key: string, value: string) Arbitrary key/value payload. If present, it will override google.firebase.fcm.v1.Message.data. An object containing a list of "key": value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
        public ?stdClass $notification = null; //Web Notification options as a JSON object. Supports Notification instance properties as defined in Web Notification API. If present, "title" and "body" fields override google.firebase.fcm.v1.Notification.title and google.firebase.fcm.v1.Notification.body.
        public ?WebpushFcmOptions $fcm_options = null; //Options for features provided by the FCM SDK for Web.


        public function getObject(): ?stdClass
        {
            if(($this->headers != null) || ($this->data != null) || ($this->notification != null) || (($this->fcm_options != null) && ($this->fcm_options->getObject() != null)))
            {
                $ret = new stdClass();

                if($this->headers != null)
                {
                    $ret->headers = $this->headers;
                }
                if($this->data != null)
                {
                    $ret->data = $this->data;
                }
                if($this->notification != null)
                {
                    $ret->notification = $this->notification;
                }
                if(($this->fcm_options != null) && ($this->fcm_options->getObject() != null))
                {
                    $ret->fcm_options = $this->fcm_options;
                }
            }
            return null;
        }
    }