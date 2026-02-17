<?php

    namespace Wixnit\DeepInsight\Resources;

    use Wixnit\Data\DBConfig;
    use Wixnit\Data\Filter;
    use Wixnit\DeepInsight\NameValuePair;
    use Wixnit\Utilities\Timespan;

    class ServerMetrics
    {
        public function __construct(
            
            /**
             * @param MetricPoint[] $series
             */
            public array $series = [],
            public float $uptimeSeconds = 0,

            /**
             * @param NameValuePair[] $topEndpoints
             */
            public array $topEndpoints = [], // endpoint -> hits or avg latency

            /**
             * @param QueryMetric[] $slowQueries
             */
            public array $slowQueries = [], // [ {query, avg_ms, count}, ... ]
        ){}

        public static function FromLogs(Timespan $timeSpan): ServerMetrics
        {
            $logs = DeepInsightRUL::Get(new Filter(['created'=> $timeSpan]));

            $rpm = ServerMetrics::getRequestPerMin($timeSpan);

            $series = [];

            foreach($logs->list as $log)
            {
                $series[] = new MetricPoint(
                    $log->created->toEpochSeconds() * 1000,
                    $log->cpuLoad,
                    $log->ramUsed,
                    $log->processingTime,
                    ServerMetrics::getRPM($log->created->toEpochSeconds(), $rpm),
                );
            }

            return new ServerMetrics(
                series: $series,
                uptimeSeconds: ServerMetrics::getServerUptime(),
                topEndpoints: ServerMetrics::getTopEndpoints($timeSpan),
                slowQueries: ServerMetrics::getSlowQueries($timeSpan),
            );
        }

        public static function getServerUptime(): int 
        {
            // Works on Linux-based systems
            /*
            if (is_readable('/proc/uptime')) 
            {
                $uptime = file_get_contents('/proc/uptime');
                if ($uptime !== false) 
                {
                    $seconds = (float) explode(' ', trim($uptime))[0];
                    return (int) floor($seconds);
                }
            }
            */

            // Fallback: try system command
            if (function_exists('shell_exec')) 
            {
                $output = shell_exec('uptime -s 2>/dev/null');
                if ($output) 
                {
                    $bootTime = strtotime(trim($output));
                    if ($bootTime !== false) {
                        return time() - $bootTime;
                    }
                }
            }

            return 0; // Uptime not available
        }

        public static function getSlowQueries(Timespan $timeSpan): array
        {
            $startDate = $timeSpan->start;
            $endDate = $timeSpan->stop;

            $sql = (new DBConfig())->getConnection();

            $slowRoutesQuery = "SELECT 
                route, 
                COUNT(id) as total_calls, 
                AVG(processingtime) as avg_time,
                AVG(ramused) as avg_ram,
                MAX(processingtime) as max_time
            FROM deepinsightrul
            WHERE created BETWEEN ? AND ? 
            GROUP BY route
            HAVING avg_time > 0.5
            ORDER BY avg_time DESC
            LIMIT 10";

            $stmt = $sql->prepare($slowRoutesQuery);
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $slowResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $ret= [];

            for($i = 0; $i < count($slowResults); $i++)
            {
                $ret[] = new QueryMetric($slowResults[$i]['route'], $slowResults[$i]['avg_time'], $slowResults[$i]['total_calls']);
            }
            return $ret;
        }

        public static function getTopEndpoints(Timespan $timeSpan): array
        {

            $startDate = $timeSpan->start;
            $endDate = $timeSpan->stop;

            $sql = (new DBConfig())->getConnection();

            $topEndpointsQuery = "SELECT 
                    route, 
                    COUNT(id) as call_count,
                    SUM(diskio) as total_disk_ops
                FROM deepinsightrul
                WHERE created BETWEEN ? AND ?
                GROUP BY route
                ORDER BY call_count DESC
                LIMIT 10";

            $stmt = $sql->prepare($topEndpointsQuery);
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $topResults = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            $ret= [];

            for($i = 0; $i < count($topResults); $i++)
            {
                $ret[] = new NameValuePair($topResults[$i]['route'], $topResults[$i]['call_count']);
            }

            return $ret;
        }

        public static function getRequestPerMin(Timespan $timeSpan)
        {
            $sql = (new DBConfig())->getConnection();

            $startTs = (int)$timeSpan->start;
            $endTs = (int)$timeSpan->stop;
            $totalSeconds = $endTs - $startTs;

            // We want exactly 10 points
            $interval = $totalSeconds / 10;
            $intervalMinutes = ($interval > 0) ? ($interval / 60) : 1;

            $query = "WITH RECURSIVE timeline AS (
                    SELECT ? AS bucket_ts
                    UNION ALL
                    SELECT bucket_ts + ?
                    FROM timeline
                    WHERE bucket_ts + ? < ?
                )
                SELECT 
                    t.bucket_ts,
                    FROM_UNIXTIME(t.bucket_ts) as bucket_time,
                    COUNT(d.id) as total_requests,
                    (COUNT(d.id) / ?) as req_per_minute
                FROM timeline t
                LEFT JOIN deepinsightrul d ON 
                    d.created >= t.bucket_ts AND 
                    d.created < (t.bucket_ts + ?)
                GROUP BY t.bucket_ts
                ORDER BY t.bucket_ts ASC";

            $stmt = $sql->prepare($query);

            // Bind parameters:
            // i = int, d = double
            $stmt->bind_param("ididid", 
                $startTs,        // timeline start
                $interval,       // timeline increment
                $interval,       // recursion check
                $endTs,          // timeline end
                $intervalMinutes,// req_per_minute calculation
                $interval        // join range
            );

            $stmt->execute();
            $list =  $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            for($i=0; $i < count($list); $i++)
            {
                //$list[$i]['bucket_time'] = (new \DateTime($list[$i]['bucket_time']))->format('c');
                if($i == 0)
                {
                    $list[$i]['end_ts'] = $list[$i]['bucket_ts'];
                    $list[$i]['start_ts'] = 0;
                }
                else if($i == count($list) - 1)
                {
                    $list[$i]['end_ts'] = ($list[$i]['bucket_ts'] + $interval);
                    $list[$i]['start_ts'] = $list[$i]['bucket_ts'];
                }
                else
                {
                    $list[$i]['end_ts'] = $list[$i]['bucket_ts'];
                    $list[$i]['start_ts'] = $list[$i-1]['bucket_ts'];
                }
            }

            return $list;
        }

        public static function getCPUandRAMUsage(Timespan $timeSpan): array
        {
            $sql = (new DBConfig())->getConnection();

            $startTs = (int)$timeSpan->start;
            $endTs = (int)$timeSpan->stop;

            $query = "SELECT
                    UNIX_TIMESTAMP(created) as timestamp,
                    cpuload as cpu_percent,
                    ramused as ram_usage_mb
                FROM deepinsightrul
                WHERE created BETWEEN FROM_UNIXTIME(?) AND FROM_UNIXTIME(?)
                ORDER BY created ASC";

            $stmt = $sql->prepare($query);
            $stmt->bind_param("ii", $startTs, $endTs);
            $stmt->execute();
            $list = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            return $list;
        }

        public static function getRPM(int $period, array $rpmList)
        {
            for($i = 0; $i < count($rpmList); $i++)
            {
                if(($period >= $rpmList[$i]['start_ts']) && ($period <= $rpmList[$i]['end_ts']))
                {
                    return $rpmList[$i]['req_per_minute'];
                }
            }
            return 0;
        }
    }