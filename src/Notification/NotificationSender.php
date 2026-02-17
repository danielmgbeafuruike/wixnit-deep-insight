<?php

    namespace Wixnit\DeepInsight\Notification;

    use Wixnit\Data\DBCollection;
    use Wixnit\DeepInsight\Auth\DeepInsightAuth;
    use Wixnit\DeepInsight\Constants;
    use Wixnit\DeepInsight\FireBase\AndroidConfig;
    use Wixnit\DeepInsight\FireBase\Enum\MessagePriority;
    use Wixnit\DeepInsight\FireBase\FireBase;
    use Wixnit\DeepInsight\FireBase\Message;
    use Wixnit\DeepInsight\FireBase\Notification;

    class NotificationSender
    {
        private $fcm = null;
        private DBCollection $tokens;

        public function __construct(string $google_service_json_path)
        {
            putenv('GOOGLE_APPLICATION_CREDENTIALS='.$google_service_json_path);

            $fcmToken = FireBase::GetAccessToken();
            $this->fcm = new FireBase($fcmToken, Constants::FCM_PROJECT_ID);

            $this->tokens = DeepInsightAuth::Get();
        }

        public function send(BaseNotification $notif): void
        {
            $notification = new DeepInsightNotification();
            $notification->title = $notif->title;
            $notification->content = $notif->content;
            $notification->isRead = false;
            $notification->type = $notif->type;
            $notification->save();

            for($i = 0; $i < count($this->tokens); $i++)
            {
                if($this->tokens[$i]->canSend($notif->category))
                {
                    $message = Message::toUser($this->tokens[$i]->fcmToken);
                    $message->notification = Notification::from(
                        $this->tokens[$i]->clientName." - ".$notif->title,
                        $notif->content." on ".$this->tokens[$i]->clientName,
                    );
                    $message->android = new AndroidConfig();
                    $message->android->data = json_decode(json_encode(['action'=>$notif->action]));
                    $message->android->priority = MessagePriority::HIGH;
                    $this->fcm->sendNotification($message);
                }
            }
        }
    }