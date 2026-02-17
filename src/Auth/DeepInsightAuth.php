<?php

    namespace Wixnit\DeepInsight\Auth;

    use Wixnit\App\Model;
    use Wixnit\Data\Filter;
    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\Utilities\Random;

    class DeepInsightAuth extends Model
    {
        public string $token;
        public string $fcmToken;
        public string $deviceId;
        public string $clientName;

        public bool $paymentReceived = true;
        public bool $renewalReceived = true;
        public bool $paymentFailed = true;
        public bool $renewalFailed = true;
        public bool $newUser = true;
        public bool $newMessage = true;
        public bool $newSubscriber = true;
        public bool $newEvent = true;
        public bool $customNotification = true;


        protected array $longText = ["fcmToken"];


        protected function onPreSave(): void
        {
            if($this->token == "")
            {
                $this->token = Random::Characters(64);

                while(DeepInsightAuth::Count(["token" => $this->token]) < 0)
                {
                    $this->token = Random::Characters(64);
                }
            }
        }

        public static function ByToken(string $token): ?DeepInsightAuth
        {
            $ret = DeepInsightAuth::Get(["token" => $token]);

            if(count($ret) > 0)
            {
                return $ret[0];
            }
            return null;
        }

        public static function ByDeviceId(string $deviceId): ?DeepInsightAuth
        {
            $ret = DeepInsightAuth::Get(["deviceId" => $deviceId]);

            if(count($ret) > 0)
            {
                return $ret[0];
            }
            return null;
        }

        public static function Create(string $fcmToken, string $deviceId): DeepInsightAuth
        {
            $auth = new DeepInsightAuth();

            $existng = DeepInsightAuth::Get(new Filter([
                'deviceid'=> $deviceId,
            ]));

            if($existng->count() > 0)
            {
                $auth = $existng[0];    
            }

            $auth->fcmToken = $fcmToken;
            $auth->deviceId = $deviceId;

            return $auth;
        }

        public static function As($auth): DeepInsightAuth
        {
            return $auth;
        }

        public function canSend(NotificationCategory $cat): bool
        {
            if($this->paymentReceived && ($cat == NotificationCategory::PAYMENT_RECEIVED))
            {
                return true;
            }
            if($this->renewalReceived && ($cat == NotificationCategory::RENEWAL_SUCCESSFULL))
            {
                return true;
            }
            if($this->paymentFailed && ($cat == NotificationCategory::PAYMENT_FAILED))
            {
                return true;
            }
            if($this->renewalFailed && ($cat == NotificationCategory::RENEWAL_FAILED))
            {
                return true;
            }
            if($this->newUser && ($cat == NotificationCategory::NEW_USER))
            {
                return true;
            }
            if($this->newMessage && ($cat == NotificationCategory::NEW_MESSAGE))
            {
                return true;
            }
            if($this->newSubscriber && ($cat == NotificationCategory::NEW_SUBSCRIBER))
            {
                return true;
            }
            if($this->newEvent && ($cat == NotificationCategory::NEW_EVENT))
            {
                return true;
            }
            if($this->customNotification && ($cat == NotificationCategory::CUSTOM_NOTIFICATION))
            {
                return true;
            }
            return false;
        }
    }