<?php

    namespace Wixnit\DeepInsight\FireBase;

    use stdClass;

    class ApnsConfig
    {
        public ?stdClass $headers = null; //map (key: string, value: string) HTTP request headers defined in Apple Push Notification Service. Refer to APNs request headers for supported headers such as apns-expiration and apns-priority. The backend sets a default value for apns-expiration of 30 days and a default value for apns-priority of 10 if not explicitly set. An object containing a list of "key": value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
        public ?stdClass $payload = null; //APNs payload as a JSON object, including both aps dictionary and custom payload. See Payload Key Reference. If present, it overrides google.firebase.fcm.v1.Notification.title and google.firebase.fcm.v1.Notification.body.
        public ?ApnsFcmOptions $fcm_options = null; //Options for features provided by the FCM SDK for iOS.


        public function getObject(): ?stdClass
        {
            if(($this->headers != null) || ($this->payload != null) || (($this->fcm_options != null) && ($this->fcm_options->getObject() != null)))
            {
                $ret = new stdClass();

                if($this->headers != null)
                {
                    $ret->headers = $this->headers;
                }
                if($this->payload != null)
                {
                    $ret->payload = $this->payload;
                }
                if(($this->fcm_options != null) && ($this->fcm_options->getObject() != null))
                {
                    $ret->fcm_options = $this->fcm_options->getObject();
                }
            }
            return null;
        }
    }