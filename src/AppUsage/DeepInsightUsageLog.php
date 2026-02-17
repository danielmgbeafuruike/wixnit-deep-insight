<?php

    namespace Wixnit\DeepInsight\AppUsage;

    use Wixnit\App\Model;

    class DeepInsightUsageLog extends Model
    {
        public string $screen;
        public int $timeSpent;
        public bool $bounced;
        public string $os;
        public string $platform;
        public string $userid;
        public int $navIndex;
        public string $date;


        protected array $mappingIndex = [
            "date"=> "_date",
        ];


        protected function onPreSave()
        {
            $this->date = date('Y-m-d');
        }



        public static function LogUsage(string $screen, int $timeSpent, bool $bounced, string $os, string $platform, string $userid, int $navIndex)
        {
            $log = new DeepInsightUsageLog();
            $log->screen = $screen;
            $log->timeSpent = $timeSpent;
            $log->bounced = $bounced;
            $log->os = $os;
            $log->platform = $platform;
            $log->userid = $userid;
            $log->navIndex = $navIndex;
            $log->save();
        }
    }