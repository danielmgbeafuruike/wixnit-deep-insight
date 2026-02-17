<?php

    namespace Wixnit\DeepInsight\FireBase;

    use Wixnit\DeepInsight\FireBase\Enum\MessagePriority;
    use stdClass;

    class AndroidConfig
    {
        public ?string $collapse_key = null; //An identifier of a group of messages that can be collapsed, so that only the last message gets sent when delivery can be resumed. A maximum of 4 different collapse keys is allowed at any given time.
        public ?MessagePriority $priority = null; //Message priority. Can take "normal" and "high" values. For more information, see Setting the priority of a message.
        public ?string $ttl = null; //How long (in seconds) the message should be kept in FCM storage if the device is offline. The maximum time to live supported is 4 weeks, and the default value is 4 weeks if not set. Set it to 0 if want to send the message immediately. In JSON format, the Duration type is encoded as a string rather than an object, where the string ends in the suffix "s" (indicating seconds) and is preceded by the number of seconds, with nanoseconds expressed as fractional seconds. For example, 3 seconds with 0 nanoseconds should be encoded in JSON format as "3s", while 3 seconds and 1 nanosecond should be expressed in JSON format as "3.000000001s". The ttl will be rounded down to the nearest second. A duration in seconds with up to nine fractional digits, ending with 's'. Example: "3.5s".
        public ?string $restricted_package_name = null; //Package name of the application where the registration token must match in order to receive the message.
        public ?stdClass $data = null; //map (key: string, value: string) Arbitrary key/value payload. If present, it will override google.firebase.fcm.v1.Message.data. An object containing a list of "key": value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count": "3" }.
        public ?AndroidNotification $notification = null; //Notification to send to android devices.
        public ?FCMOPtions $fcm_options= null; //Options for features provided by the FCM SDK for Android.
        public ?bool $direct_boot_ok = null; //If set to true, messages will be allowed to be delivered to the app while the device is in direct boot mode. See Support Direct Boot mode.
    
        public function getObject(): ?stdClass
        {
            if(($this->collapse_key != null) || ($this->priority != null) || ($this->ttl != null) 
                || ($this->restricted_package_name != null) || ($this->data != null)
                || ($this->direct_boot_ok != null) || (($this->notification != null) && ($this->notification->getObject())) 
                || (($this->fcm_options != null) && ($this->fcm_options->getObject())))
            {
                $ret = new stdClass();
                
                if($this->collapse_key != null)
                {
                    $ret->collapse_key = $this->collapse_key;
                }
                if($this->ttl != null)
                {
                    $ret->ttl = $this->ttl;
                }
                if($this->restricted_package_name != null)
                {
                    $ret->restricted_package_name = $this->restricted_package_name;
                }
                if($this->data != null)
                {
                    $ret->data = $this->data;
                }
                if($this->direct_boot_ok != null)
                {
                    $ret->direct_boot_ok = $this->direct_boot_ok;
                }
                if($this->priority != null)
                {
                    $ret->priority = $this->priority;
                }



                if(($this->notification != null) && ($this->notification->getObject()))
                {
                    $ret->notification = $this->notification->getObject();
                }
                if(($this->fcm_options != null) && ($this->fcm_options->getObject()))
                {
                    $ret->fcm_options = $this->fcm_options->getObject();
                }
                return $ret;
            }
            return null;
        }
    }