<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class PaymentNotification extends BaseNotification
    {
        public function __construct(
            public bool $isSuccessfull,
            public float $amount,
            public string $currency,
            public string $customersName,
            public string $channel
        )
        {
            $this->title = $isSuccessfull ? "Successfull Payment" : "Failed Payment";
            $this->content = "A payment of ".$currency.number_format($amount)." from ".$customersName." made through ".$channel.(($isSuccessfull) ? " completed successfully" : " failed");
            $this->action = "payment_alert";
            $this->category = $isSuccessfull ? NotificationCategory::PAYMENT_RECEIVED : NotificationCategory::PAYMENT_FAILED;
            $this->type = $isSuccessfull ? NotificationType::INFO : NotificationType::ALERT;
        }
    }