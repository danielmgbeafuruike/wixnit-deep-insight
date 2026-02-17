<?php

    namespace Wixnit\DeepInsight\Subscribers;

    use Wixnit\App\Model;
    use Wixnit\Data\DBConfig;

    class DeepInsightSubscriber extends Model
    {
        public string $email;
        public string $phone;
        public string $topic;


        public static function Topics(): array
        {
            $ret = [];
            $res = (new DBConfig())->getConnection()->query("SELECT topic FROM deepinsightsubscriber GROUP BY topic");

            while(($d = $res->fetch_assoc()) != null)
            {
                $ret[] = $d['topic'];
            }
            return $ret;
        }
    }