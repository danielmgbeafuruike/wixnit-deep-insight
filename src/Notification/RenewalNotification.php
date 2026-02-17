<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\DeepInsight\Enum\NotificationCategory;
    use Wixnit\DeepInsight\Enum\NotificationType;

    class RenewalNotification extends BaseNotification
    {
        public function __construct(
            public bool $isSuccessfull,
            public float $amount,
            public string $currency,
            public string $customersName,
            public string $channel
        )
        {
            $this->title = $isSuccessfull ? "Successfull Renewal" : "Renewal Failed";
            $this->content = "A renewal of ".$currency.number_format($amount)." from ".$customersName." made through ".$channel.(($isSuccessfull) ? " completed successfully" : " failed");
            $this->action = "renewal_alert";
            $this->category = $isSuccessfull ? NotificationCategory::RENEWAL_SUCCESSFULL : NotificationCategory::RENEWAL_FAILED;
            $this->type = $isSuccessfull ? NotificationType::INFO : NotificationType::ALERT;
        }
    }