<?php

    namespace Wixnit\DeepInsight;

    use mysqli;
    use Wixnit\App\api;
    use Wixnit\Data\DBConfig;
    use Wixnit\Data\DBMigrator;
    use Wixnit\DeepInsight\AppUsage\DeepInsightActionLog;
    use Wixnit\DeepInsight\AppUsage\DeepInsightUsageLog;
    use Wixnit\DeepInsight\Auth\DeepInsightAuth;
    use Wixnit\DeepInsight\Auth\DeepInsightAuthBuffer;
    use Wixnit\DeepInsight\Log\DeepInsightAPILog;
    use Wixnit\DeepInsight\Log\DeepInsightErrorLog;
    use Wixnit\DeepInsight\Log\DeepInsightEventLog;
    use Wixnit\DeepInsight\Messaging\DeepInsightMessage;
    use Wixnit\DeepInsight\Notification\DeepInsightNotification;
    use Wixnit\DeepInsight\Resources\DeepInsightRUL;
    use Wixnit\DeepInsight\Subscribers\DeepInsightSubscriber;
    use Wixnit\DeepInsight\Traffic\DeepInsightVisit;
    use Wixnit\DeepInsight\Traffic\FrontEndTrafficLog;

    class Setup
    {
        public static function RunMigrations(mysqli | null $db = null): void
        {
            $migrator = new DBMigrator(($db != null) ? $db : ((new DBConfig())->getConnection()));
            $migrator->mapClass(DeepInsightAuth::class);
            $migrator->mapClass(DeepInsightAuthBuffer::class);
            $migrator->mapClass(DeepInsightNotification::class);
            $migrator->mapClass(DeepInsightMessage::class);
            $migrator->mapClass(DeepInsightEventLog::class);
            $migrator->mapClass(DeepInsightErrorLog::class);
            $migrator->mapClass(DeepInsightSubscriber::class);
            $migrator->mapClass(DeepInsightVisit::class);
            $migrator->mapClass(DeepInsightAPILog::class);
            $migrator->mapClass(FrontEndTrafficLog::class);
            $migrator->mapClass(DeepInsightRUL::class);
            $migrator->mapClass(DeepInsightUsageLog::class);
            $migrator->mapClass(DeepInsightActionLog::class);
        }
    }