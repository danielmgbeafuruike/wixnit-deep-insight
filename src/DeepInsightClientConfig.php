<?php

    namespace Wixnit\DeepInsight;

    use Wixnit\DeepInsight\Auth\DeepInsightAuth;

    class DeepInsightClientConfig
    {
        public function __construct(
            public string $client_name,
            public bool $payment_received,
            public bool $renewal_received,
            public bool $payment_failed,
            public bool $renewwal_failed,
            public bool $new_user,
            public bool $new_message,
            public bool $new_subscriber,
            public bool $new_event,
            public bool $custom_notification,
        ){}

        public static function FromAuth(DeepInsightAuth $auth): DeepInsightClientConfig
        {
            $ret= new DeepInsightClientConfig(
                $auth->clientName,
                $auth->paymentReceived,
                $auth->renewalReceived,
                $auth->paymentFailed,
                $auth->renewalFailed,
                $auth->newUser,
                $auth->newMessage,
                $auth->newSubscriber,
                $auth->newEvent,
                $auth->customNotification,
            );
            return $ret;
        }
    }