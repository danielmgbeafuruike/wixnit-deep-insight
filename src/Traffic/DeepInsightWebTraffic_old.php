<?php

    namespace Wixnit\DeepInsight\Traffic;

    use Wixnit\Data\DBConfig;
    use Wixnit\Data\Filter;
    use Wixnit\DeepInsight\DeepInsightEnv;
    use Wixnit\DeepInsight\NameValuePair;
    use Wixnit\DeepInsight\Traffic\UserIntent;
    use Wixnit\DeepInsight\Traffic\VisitChannel;
    use Wixnit\Utilities\Timespan;

    class DeepInsightWebTraffic
    {
        public function __construct(
            public int $totalVisits,
            public int $botSessions, //bots that visit the website
            public int $pagesPerSession, //pages visited by a user
            public float $bounceRate, //bounce rate
            public int $avgDuration, //time spent by a user
            public int $newVisits,
            public int $returingVisits,
            public UserIntent $userIntent,

            /**
             * @param NameValuePair[] $referrer
             * shows the referer as it is. full url
             */
            public array $referrer = [],
            public VisitChannel $channel,

            /**
             * @param NameValuePair[] $source
             * shows visits like from google, facebook, whatsapp, etc
             */
            public array $source = [],

            /**
             * @param NameValuePair[] $browser
             * shows browsers like Chrome, Firefox, Safari, Edge
             */
            public array $browser = [],

            /**
             * @param NameValuePair[] $devices
             * shows devices type like PC, Mobile, Tablet, Others 
             */
            public array $devices = [],

            /**
             * @param NameValuePair[] $os
             * shows operating systems like Android, IOS, Windows, macOS, Linux, Ubuntu, Chrome OS
             */
            public array $os = [],

            /**
             * @param NameValuePair[] $os
             * shows operating systems like Android, IOS, Windows, macOS, Linux, Ubuntu, Chrome OS
             */
            public array $region = [],

            /**
             * @param NameValuePair[] $pageRanking
             * ranking of pages by visits
             */
            public array $pageRanking = [],

            /**
             * @param NameValuePair[] $weeklyVisit
             */
            public array $weeklyVisits = [],

            /**
             * @param NameValuePair[] $monthlyVisits
             */
            public array $monthlyVisits = [],

            /**
             * @param  NameValuePair[] $visitTrends
             */
            public array $visitTrend = [],
        ){}


        public static function FromLogs(Timespan $timeSpan): DeepInsightWebTraffic
        {
            $logs = DeepInsightVisit::Get(new Filter(['created'=> $timeSpan, 'isbot'=> false]));
            $frontLogs = FrontEndTrafficLog::Get(new Filter(['created'=> $timeSpan]));

            $timeSplit = $timeSpan->splitSpan(10);

            $osAssoc = [];
            $os = [];

            $browserAssoc = [];
            $browsers = [];

            $devicesAssoc = [];
            $devices = [];

            $regionAssoc = [];
            $regions = [];

            $refererAsoc = [];
            $referer = [];

            $visitTrend =  [];
            $visitTrendAssoc = [];

            $weeklyTrend = [
                new NameValuePair("sun", 0),
                new NameValuePair("mon", 0),
                new NameValuePair("tue", 0),
                new NameValuePair("wed", 0),
                new NameValuePair("thu", 0),
                new NameValuePair("fri", 0),
                new NameValuePair("sat", 0),
            ];

            $monthlyTrend = [
                new NameValuePair("jan", 0),
                new NameValuePair("feb", 0),
                new NameValuePair("mar", 0),
                new NameValuePair("apr", 0),
                new NameValuePair("may", 0),
                new NameValuePair("jun", 0),
                new NameValuePair("jul", 0),
                new NameValuePair("aug", 0),
                new NameValuePair("sep", 0),
                new NameValuePair("oct", 0),
                new NameValuePair("nov", 0),
                new NameValuePair("dec", 0),
            ];



            for($i = 0; $i < count($timeSplit); $i++)
            {
                $visitTrendAssoc[$timeSplit[$i]->start] = 0;

                for($j = 0; $j < $logs->count(); $j++)
                {
                    if(($logs[$j]->created->toEpochSeconds() >= $timeSplit[$i]->start) && ($logs[$j]->created->toEpochSeconds() <= $timeSplit[$i]->stop))
                    {
                        $visitTrendAssoc[$timeSplit[$i]->start]++;
                    }
                }
            }


            for($i = 0; $i < $logs->count(); $i++)
            {
                //os
                if(isset($osAssoc[$logs[$i]->os]))
                {
                    $osAssoc[$logs[$i]->os]++;
                }
                else
                {
                    $osAssoc[$logs[$i]->os] = 1;
                }

                //browser
                if(isset($browserAssoc[$logs[$i]->browser == "" ? "Others" : $logs[$i]->browser]))
                {
                    $browserAssoc[$logs[$i]->browser == "" ? "Others" : $logs[$i]->browser]++;
                }
                else
                {
                    $browserAssoc[$logs[$i]->browser == "" ? "Others" : $logs[$i]->browser] = 1;
                }

                //devices
                if(isset($devicesAssoc[$logs[$i]->deviceName == "" ? "Others" : $logs[$i]->deviceName]))
                {
                    $devicesAssoc[$logs[$i]->deviceName == "" ? "Others" : $logs[$i]->deviceName]++;
                }
                else
                {
                    $devicesAssoc[$logs[$i]->deviceName == "" ? "Others" : $logs[$i]->deviceName] = 1;
                }

                //regions
                if(isset($regionAssoc[$logs[$i]->country == "" ? "Others" : $logs[$i]->country]))
                {
                    $regionAssoc[$logs[$i]->country == "" ? "Others" : $logs[$i]->country]++;
                }
                else
                {
                    $regionAssoc[$logs[$i]->country == "" ? "Others" : $logs[$i]->country] = 1;
                }

                //referer
                if(isset($refererAsoc[$logs[$i]->referer == "" ? "Others" : $logs[$i]->referer]))
                {
                    $refererAsoc[$logs[$i]->referer == "" ? "Others" : $logs[$i]->referer]++;
                }
                else
                {
                    $refererAsoc[$logs[$i]->referer == "" ? "Others" : $logs[$i]->referer] = 1;
                }





                
            }

            $osKeys = array_keys($osAssoc);

            for($i = 0; $i < count($osKeys); $i++)
            {
                $os[] = new NameValuePair($osKeys[$i], $osAssoc[$osKeys[$i]]);
            }


            $browserKeys = array_keys($browserAssoc);

            for($i = 0; $i < count($browserKeys); $i++)
            {
                $browsers[] = new NameValuePair($browserKeys[$i], $browserAssoc[$browserKeys[$i]]);
            }


            $deviceKeys = array_keys($devicesAssoc);

            for($i = 0; $i < count($deviceKeys); $i++)
            {
                $devices[] = new NameValuePair($deviceKeys[$i], $devicesAssoc[$deviceKeys[$i]]);
            }


            $regionKeys = array_keys($regionAssoc);

            for($i = 0; $i < count($regionKeys); $i++)
            {
                $regions[] = new NameValuePair($regionKeys[$i], $regionAssoc[$regionKeys[$i]]);
            }


            $refererKeys = array_keys($refererAsoc);

            for($i = 0; $i < count($refererKeys); $i++)
            {
                $referer[] = new NameValuePair($refererKeys[$i], $refererAsoc[$refererKeys[$i]]);
            }



            $visitTrendKeys = array_keys($visitTrendAssoc);

            for($i = 0; $i < count($visitTrendKeys); $i++)
            {
                $visitTrend[] = new NameValuePair((date("d M", intval($visitTrendKeys[$i]))), $visitTrendAssoc[$visitTrendKeys[$i]]);
            }


            for($i = 0; $i < $logs->count(); $i++)
            {
                for($j = 0; $j < count($weeklyTrend); $j++)
                {
                    if($weeklyTrend[$j]->name == strtolower(date("D", $logs[$i]->created->toEpochSeconds())))
                    {
                        $weeklyTrend[$j]->value++;
                        break;
                    }
                }
            }


            for($i = 0; $i < $logs->count(); $i++)
            {
                for($j = 0; $j < count($monthlyTrend); $j++)
                {
                    if($monthlyTrend[$j]->name == strtolower(date("M", $logs[$i]->created->toEpochSeconds())))
                    {
                        $monthlyTrend[$j]->value++;
                        break;
                    }
                }
            }


            $channels = new VisitChannel(
                0,
                0,
                0,
                0,
                0,
            );

            for($i = 0; $i < $logs->count(); $i++)
            {
                if((strpos(strtolower($logs[$i]->referer), "google")) || 
                (strpos(strtolower($logs[$i]->referer), "bing")) ||
                (strpos(strtolower($logs[$i]->referer), "yahoo")))
                {
                    $channels->searchEngines++;
                }
                else if((strpos(strtolower($logs[$i]->referer), "chatgpt")) || 
                (strpos(strtolower($logs[$i]->referer), "gemini")) ||
                (strpos(strtolower($logs[$i]->referer), "claud")) ||
                (strpos(strtolower($logs[$i]->referer), "qwen")))
                {
                    $channels->aiPlatform++;
                }
                else if((strtolower(trim($logs[$i]->referer)) == "") || 
                (strpos(strtolower($logs[$i]->referer), $_SERVER['HTTP_HOST'])) ||
                (strpos(strtolower($logs[$i]->referer), DeepInsightEnv::getHostName()) !== false))
                {
                    $channels->organic++;
                }
                else
                {
                    $channels->others++;
                }
            }


            $sources = [];
            $sourcesAssoc = [];

            for($i = 0; $i < $logs->count(); $i++)
            {
                if(strpos(strtolower($logs[$i]->referer), "google"))
                {
                    if(isset($sourcesAssoc["google"]))
                    {
                        $sourcesAssoc["google"]++;
                    }
                    else
                    {
                        $sourcesAssoc["google"] = 1;
                    }
                }
                if(strpos(strtolower($logs[$i]->referer), "bing"))
                {
                    if(isset($sourcesAssoc["bing"]))
                    {
                        $sourcesAssoc["bing"]++;
                    }
                    else
                    {
                        $sourcesAssoc["bing"] = 1;
                    }
                }
                if(strpos(strtolower($logs[$i]->referer), "yahoo"))
                {
                    if(isset($sourcesAssoc["yahoo"]))
                    {
                        $sourcesAssoc["yahoo"]++;
                    }
                    else
                    {
                        $sourcesAssoc["yahoo"] = 1;
                    }
                }
                if(strpos(strtolower($logs[$i]->referer), "facebook"))
                {
                    if(isset($sourcesAssoc["facebook"]))
                    {
                        $sourcesAssoc["facebook"]++;
                    }
                    else
                    {
                        $sourcesAssoc["facebook"] = 1;
                    }
                }

                if(strpos(strtolower($logs[$i]->referer), "chatgpt"))
                {
                    if(isset($sourcesAssoc["chatgpt"]))
                    {
                        $sourcesAssoc["chatgpt"]++;
                    }
                    else
                    {
                        $sourcesAssoc["chatgpt"] = 1;
                    }
                }

                if(strpos(strtolower($logs[$i]->referer), "gemini"))
                {
                    if(isset($sourcesAssoc["gemini"]))
                    {
                        $sourcesAssoc["gemini"]++;
                    }
                    else
                    {
                        $sourcesAssoc["gemini"] = 1;
                    }
                }

                if(strpos(strtolower($logs[$i]->referer), "claud"))
                {
                    if(isset($sourcesAssoc["claud"]))
                    {
                        $sourcesAssoc["claud"]++;
                    }
                    else
                    {
                        $sourcesAssoc["claud"] = 1;
                    }
                }

                if(strpos(strtolower($logs[$i]->referer), "qwen"))
                {
                    if(isset($sourcesAssoc["qwen"]))
                    {
                        $sourcesAssoc["qwen"]++;
                    }
                    else
                    {
                        $sourcesAssoc["qwen"] = 1;
                    }
                }

                if((strtolower(trim($logs[$i]->referer)) == "") || 
                (strpos(strtolower($logs[$i]->referer), $_SERVER['HTTP_HOST'])) ||
                (strpos(strtolower($logs[$i]->referer), DeepInsightEnv::getHostName()) !== false))
                {
                    if(isset($sourcesAssoc["organic"]))
                    {
                        $sourcesAssoc["organic"]++;
                    }
                    else
                    {
                        $sourcesAssoc["organic"] = 1;
                    }
                }
            }

            $sourcesKey = array_keys($sourcesAssoc);

            for($i = 0; $i < count($sourcesKey); $i++)
            {
                $sources[] = new NameValuePair($sourcesKey[$i], $sourcesAssoc[$sourcesKey[$i]]);
            }


            //front traffic log parts
            $highIntent = 0;
            $mediumIntent = 0;
            $lowIntent = 0;


            $pRank = [];

            for($i = 0; $i < $frontLogs->count(); $i++)
            {
                if(isset($pRank[$logs[$i]->page]))
                {
                    $pRank[$logs[$i]->page]++;
                }
                else
                {
                    $pRank[$logs[$i]->page] = 1;
                }
            }

            $pageRanking = [];
            $pKeys= array_keys($pRank);

            for($i = 0; $i < count($pKeys); $i++)
            {
                $pageRanking[] = new NameValuePair($pKeys[$i], $pRank[$pKeys[$i]]);
            }


            return new DeepInsightWebTraffic(
                DeepInsightVisit::Count(new Filter(['isbot'=> false, 'created'=> $timeSpan])),
                DeepInsightVisit::Count(new Filter(['isbot'=> true, 'created'=> $timeSpan])),
                DeepInsightWebTraffic::pageVisitPerSession($timeSpan), //pages visited by a user
                DeepInsightWebTraffic::bounceRate($timeSpan), //bounce rate
                DeepInsightWebTraffic::avgDuration($timeSpan), //time spent by a user
                DeepInsightVisit::Count(new Filter(['isrevisit'=> false, 'isbot'=> false, 'created'=> $timeSpan])),
                DeepInsightVisit::Count(new Filter(['isrevisit'=> true, 'isbot'=> false, 'created'=> $timeSpan])),
                DeepInsightWebTraffic::getUserIntent($timeSpan),
                //referrer
                $referer,
                $channels,
                $sources,
                $browsers,
                $devices,
                $os,
                $regions,
                $pageRanking,

                //set weekly visit trens
                $weeklyTrend,
                
                
                //set monthly visit trens
                $monthlyTrend,
                
                //set visit trends
                $visitTrend,
            );
        }


        

        public static function avgDuration(Timespan $timeSpan): float
        {
            $start = $timeSpan->start;
            $stop  = $timeSpan->stop;

            $mysqli = (new DBConfig())->getConnection();

            $stmt = $mysqli->prepare("SELECT AVG(session_duration) AS avg_duration_per_session
                FROM (
                    SELECT sessionid, SUM(duration) AS session_duration
                    FROM frontendtrafficlog
                    WHERE created BETWEEN ? AND ?
                    GROUP BY sessionid
                ) t");

            if (!$stmt) {
                throw new \Exception($mysqli->error);
            }

            $stmt->bind_param('ss', $start, $stop);
            $stmt->execute();

            $result = $stmt->get_result();
            $row = $result->fetch_assoc();

            return (float) ($row['avg_duration_per_session'] ?? 0);
        }


        public static function bounceRate(Timespan $timeSpan)
        {
            $start = $timeSpan->start;
            $stop = $timeSpan->stop;

            $sql = (new DBConfig())->getConnection();

            $res = $sql->query("SELECT COUNT(*) AS total_sessions, SUM(CASE WHEN page_count = 1 THEN 1 ELSE 0 END) AS single_page_sessions
                FROM (
                    SELECT sessionid, COUNT(*) AS page_count
                    FROM frontendtrafficlog
                    WHERE created BETWEEN '$start' AND '$stop'
                    GROUP BY sessionid
                ) t");

            $row = $res->fetch_assoc();

            if($row['total_sessions'] == 0)
            {
                return 0;
            }
            return ($row['single_page_sessions'] / $row['total_sessions']) * 100;
        }

        public static function pageVisitPerSession(Timespan $timeSpan): float
        {
            $start = $timeSpan->start;
            $stop  = $timeSpan->stop;

            $mysqli = (new DBConfig())->getConnection();

            $stmt = $mysqli->prepare("SELECT AVG(page_count) AS avg_pages_per_session
                FROM (
                    SELECT sessionid, COUNT(*) AS page_count
                    FROM frontendtrafficlog
                    WHERE created BETWEEN ? AND ?
                    GROUP BY sessionid
                ) t");

            $stmt->bind_param('ss', $start, $stop);
            $stmt->execute();

            $result = $stmt->get_result();
            return (float) $result->fetch_assoc()['avg_pages_per_session'];
        }


        public static function getUserIntent(Timespan $timeSpan): UserIntent
        {
            $start = $timeSpan->start;
            $stop = $timeSpan->stop;

            $sql = (new DBConfig())->getConnection();

            $stmt = $sql->query("SELECT 
                    sessionid,
                    COUNT(*) AS pages_visited,
                    SUM(duration) AS total_duration
                FROM frontendtrafficlog
                WHERE created BETWEEN '$start' AND '$stop'
                GROUP BY sessionid");

            $results = [];

            while (($row = $stmt->fetch_assoc()) != null) 
            {
                $pages = (int) $row['pages_visited'];
                $duration = (int) $row['total_duration'];

                if ($pages === 1 || $duration < 30) {
                    $intent = 'low';
                } elseif ($pages <= 3 && $duration < 120) {
                    $intent = 'medium';
                } else {
                    $intent = 'high';
                }

                $results[] = [
                    'sessionid' => $row['sessionid'],
                    'pages_visited' => $pages,
                    'total_duration' => $duration,
                    'intent' => $intent
                ];
            }

            $high = 0;
            $medium = 0;
            $low = 0;

            for($i = 0; $i < count($results); $i++)
            {
                if($results[$i]['intent'] == 'high')
                {
                    $high++;
                }
                else if($results[$i]['intent'] == 'medium')
                {
                    $medium++;
                }
                else if($results[$i]['intent'] == 'low')
                {
                    $low++;
                }
            }

            $ret = new UserIntent(0, 0, 0);

            if($low + $medium + $high > 0)
            {
                $ret->high = ($high / ($low + $medium + $high)) * 100;
                $ret->medium = ($medium / ($low + $medium + $high)) * 100;
                $ret->low = ($low / ($low + $medium + $high)) * 100;
            }
            return $ret;
        }
    }