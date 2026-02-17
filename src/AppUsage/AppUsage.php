<?php

    namespace Wixnit\DeepInsight\AppUsage;

    use Wixnit\Data\DBConfig;
    use Wixnit\Utilities\Timespan;

    class AppUsage
    {
        public function __construct(
            public AppUsageSummary $summary,
            
            public SessionStats $sessionStats,

            /**
             * @param UsageTrend[] $usageTrend
             */
            public array $usageTrend = [],
            
            /**
             * @param ScreenUsage[] $screens
             */
            public array $screens = [],

            /**
             * @param ActionUsage[] $actions
             */
            public array $actions = [],

            /**
             * @param HeatmapCell[] $heatMap
             */
            public array $heatMap = [],

            /**
             * @param DeviceUsage[] $devices
             */
            public array $devices = [],

            /**
             * @param CohortRetention[] $cohorts
             */
            public array $cohorts = [],
        ){}


        public static function fromLogs(Timespan $timespan): AppUsage
        {
            return new AppUsage(
                new AppUsageSummary(
                    AppUsage::getDAU($timespan),
                    AppUsage::getPeriodActive($timespan),
                    AppUsage::getPeriodActive($timespan),
                    AppUsage::getAvgDuration($timespan),
                    AppUsage::getReturningUsersPercent($timespan),
                    AppUsage::getChurnRisk(7)
                ),
                new SessionStats(
                    AppUsage::getAvgSessionsPerUser($timespan),
                    AppUsage::getAvgDuration($timespan),
                    AppUsage::getHourlyStats($timespan),
                    AppUsage::getDropOffStats($timespan),
                ),
                AppUsage::getUsageTrend($timespan),
                AppUsage::getScreenUsageReport($timespan),
                AppUsage::getActionUsageReport($timespan),
                AppUsage::getActivityHeatmap($timespan),
                AppUsage::getDeviceDistribution($timespan),
                AppUsage::getWeeklyCohortRetention($timespan),
            );
        }





        public static function getDAU(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            $query = "SELECT AVG(daily_total) FROM (
                    SELECT COUNT(DISTINCT userid) as daily_total 
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY DATE(_date)
                  ) AS daily_counts";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            return (float)($stmt->get_result()->fetch_row()[0] ?? 0.0);
        }

        // WAU/MAU: Distinct users over a rolling window
        public static function getPeriodActive(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            $stmt = $db->prepare("SELECT COUNT(DISTINCT userid) FROM deepinsightusagelog 
                                        WHERE created BETWEEN DATE_SUB(?, INTERVAL ? DAY) AND ?");
            $stmt->bind_param("sis", $endDate, $days, $endDate);
            $stmt->execute();
            return (int)$stmt->get_result()->fetch_row()[0];
        }

        // Average of the 'timespent' column
        public static function getAvgDuration(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            $stmt = $db->prepare("SELECT AVG(timespent) FROM deepinsightusagelog WHERE created BETWEEN ? AND ?");
            $stmt->bind_param("ss", $start, $end);
            $stmt->execute();
            return round((float)$stmt->get_result()->fetch_row()[0], 2);
        }

        /**
         * Returning Users: Users who appear on at least TWO different days
         */
        public static function getReturningUsersPercent(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            $query = "SELECT 
                    (COUNT(DISTINCT CASE WHEN days_active >= 2 THEN userid END) / COUNT(DISTINCT userid)) * 100
                  FROM (
                    SELECT userid, COUNT(DISTINCT FROM_UNIXTIME(created, '%Y-%m-%d')) as days_active 
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY userid
                  ) AS user_behavior";
        
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            return round((float)($stmt->get_result()->fetch_row()[0] ?? 0.0), 2);
        }

        /**
         * Churn Risk: % of users whose last activity was > X days ago
         */
        public static function getChurnRisk(int $daysThreshold) 
        {
            $db = (new DBConfig())->getConnection();

            $thresholdSeconds = $daysThreshold * 86400;
            $cutoff = time() - $thresholdSeconds;

            $query = "SELECT 
                        (COUNT(DISTINCT CASE WHEN last_seen < ? THEN userid END) / COUNT(DISTINCT userid)) * 100
                    FROM (
                        SELECT userid, MAX(created) as last_seen 
                        FROM deepinsightusagelog 
                        GROUP BY userid
                    ) AS loyalty_table";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("i", $cutoff);
            $stmt->execute();
            return round((float)($stmt->get_result()->fetch_row()[0] ?? 0.0), 2);
        }



        //-------------------------------------------------------------------------------------------------


        /**
         * 1. Average Sessions Per User
         * We define a session as a unique combination of user + day. 
         * (If your app has a specific session_id, swap the COUNT logic).
         */
        public static function getAvgSessionsPerUser(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;


            $query = "SELECT AVG(session_count) FROM (
                        SELECT userid, COUNT(DISTINCT FROM_UNIXTIME(created, '%Y-%m-%d')) as session_count 
                        FROM deepinsightusagelog 
                        WHERE created BETWEEN ? AND ?
                        GROUP BY userid
                    ) AS user_sessions";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            return round((float)($stmt->get_result()->fetch_row()[0] ?? 0.0), 2);
        }

        /**
         * 2. Hourly Stats (0-23)
         * Returns an array of 24 integers representing log frequency per hour.
         */
        public static function getHourlyStats(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            $stats = array_fill(0, 24, 0); // Initialize with zeros
            
            $query = "SELECT HOUR(FROM_UNIXTIME(created)) as hr, COUNT(*) as cnt 
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY hr ORDER BY hr ASC";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $startEpoch, $endEpoch);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                $stats[(int)$row['hr']] = (int)$row['cnt'];
            }
            return $stats;
        }

        /**
         * 3. Drop-off Percentage
         * Calculated as: (Users who LAST saw this screen / Users who EVER saw this screen) * 100
         */
        public static function getDropOffStats(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;


            $query = "SELECT 
                        screen,
                        COUNT(DISTINCT userid) as total_viewers,
                        SUM(is_last_screen) as total_exits
                    FROM (
                        SELECT 
                            userid, 
                            screen,
                            -- Check if this log entry was the user's final action in the period
                            IF(created = MAX(created) OVER(PARTITION BY userid), 1, 0) as is_last_screen
                        FROM deepinsightusagelog
                        WHERE created BETWEEN ? AND ?
                    ) AS exit_analysis
                    GROUP BY screen
                    ORDER BY total_exits DESC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();

            $dropOffs = [];
            while ($row = $result->fetch_assoc()) {
                $percent = ($row['total_viewers'] > 0) ? ($row['total_exits'] / $row['total_viewers']) * 100 : 0;
                $dropOffs[] = [
                    'screen' => $row['screen'],
                    'percentage' => round($percent, 2)
                ];
            }
            return $dropOffs;
        }




        //---------------------------------------------------------------------------------------------------------

        /**
         * Generates an array of UsageTrend objects for each day in the range
         */
        public static function getUsageTrend(Timespan $timespan): array 
        {
            $start = $timespan->start;
            $end = $timespan->stop;

            $trends = [];
            
            // Move from start to stop day-by-day
            // We use 86400 seconds (1 day) as the increment
            for ($currentDay = $start; $currentDay <= $end; $currentDay += 86400) {
                
                // Define the specific windows for this specific point in the timeline
                $dayStart = strtotime("today", $currentDay);
                $dayEnd   = strtotime("tomorrow", $dayStart) - 1;
                
                $wauStart = $dayEnd - (7 * 86400);
                $mauStart = $dayEnd - (30 * 86400);

                $trends[] = new UsageTrend(
                    date: $dayStart,
                    dailyActiveUsers:   AppUsage::getDistinctCount($dayStart, $dayEnd),
                    weeklyActiveUsers:  AppUsage::getDistinctCount($wauStart, $dayEnd),
                    monthlyActiveUsers: AppUsage::getDistinctCount($mauStart, $dayEnd)
                );
            }

            return $trends;
        }

        public static function getDistinctCount(int $start, int $end) 
        {
            $db = (new DBConfig())->getConnection();

            $stmt = $db->prepare("SELECT COUNT(DISTINCT userid) FROM deepinsightusagelog WHERE created BETWEEN ? AND ?");
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_row()[0];
            return (int)($res ?? 0);
        }


        //------------------------------------------------------------------------

        public static function getScreenUsageReport(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;


            $screens = [];

            // 1. Get General Stats per Screen
            $query = "SELECT 
                        screen, 
                        COUNT(*) as total_views, 
                        AVG(timespent) as avg_duration,
                        (SUM(CASE WHEN bounced = 1 THEN 1 ELSE 0 END) / COUNT(*)) * 100 as bounce_rate
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY screen";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $mainResult = $stmt->get_result();

            // 2. Get 7-Day Trend Data for all screens in one go to avoid N+1 query issues
            $trendData = AppUsage::getBulk7DayTrends($end);

            while ($row = $mainResult->fetch_assoc()) {
                $name = $row['screen'];
                
                $screens[] = new ScreenUsage(
                    screenName: $name,
                    totalViews: (int)$row['total_views'],
                    avgDuration: round((float)$row['avg_duration'], 2),
                    bounceRate: round((float)$row['bounce_rate'], 2),
                    last7DaysTrend: $trendData[$name] ?? array_fill(0, 7, 0)
                );
            }

            return $screens;
        }

        /**
         * Helper to fetch the last 7 days of view counts, grouped by screen and day
         */
        public static function getBulk7DayTrends($stopEpoch) 
        {
            $db = (new DBConfig())->getConnection();

            $sevenDaysAgo = $stopEpoch - (7 * 86400);
            $trends = [];

            $query = "SELECT 
                        screen, 
                        FROM_UNIXTIME(created, '%Y-%m-%d') as day_label, 
                        COUNT(*) as daily_views
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY screen, day_label
                    ORDER BY day_label ASC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $sevenDaysAgo, $stopEpoch);
            $stmt->execute();
            $result = $stmt->get_result();

            // Initialize temporary storage to align dates
            while ($row = $result->fetch_assoc()) {
                $trends[$row['screen']][] = (int)$row['daily_views'];
            }

            // Ensure every array has exactly 7 elements (padding with zeros if screen wasn't active)
            foreach ($trends as $screen => $data) {
                if (count($data) < 7) {
                    $trends[$screen] = array_pad($data, -7, 0); 
                }
            }

            return $trends;
        }


        //-------------------------------------------------------------------------

    
        /**
         * Gets a list of unique platforms and the count of unique users for each.
         * * @param int $startEpoch
         * @param int $stopEpoch
         * @return DeviceUsage[]
         */
        public static function getDeviceDistribution(Timespan $timespan): array 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;


            $deviceStats = [];

            $query = "SELECT 
                        platform, 
                        COUNT(DISTINCT userid) as unique_user_count
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY platform
                    ORDER BY unique_user_count DESC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                // Handle null or empty platform strings if they exist
                $platformName = !empty($row['platform']) ? $row['platform'] : 'Unknown';

                $deviceStats[] = new DeviceUsage(
                    platform: $platformName,
                    users: (int)$row['unique_user_count']
                );
            }

            return $deviceStats;
        }


        //---------------------------------------------------------------------------------------

        /**
         * Gets activity counts grouped by day of week and hour.
         * @param int $startEpoch
         * @param int $stopEpoch
         * @return HeatmapCell[]
         */
        public static function getActivityHeatmap(Timespan $timespan): array 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;


            $heatmap = [];

            // MySQL WEEKDAY(): 0 = Monday, 1 = Tuesday, ... 6 = Sunday
            $query = "SELECT 
                        HOUR(FROM_UNIXTIME(created)) as hr, 
                        WEEKDAY(FROM_UNIXTIME(created)) as wkday, 
                        COUNT(*) as activity_count
                    FROM deepinsightusagelog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY wkday, hr
                    ORDER BY wkday ASC, hr ASC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $heatmap[] = new HeatmapCell(
                    hour: (int)$row['hr'],
                    day: (int)$row['wkday'],
                    count: (int)$row['activity_count']
                );
            }

            return $heatmap;
        }

        //----------------------------------------------------------------------------------------

        public static function getWeeklyCohortRetention(Timespan $timespan): array 
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            // 1. Identify the 'Birth Week' for every user
            // 2. Track activity weeks relative to that birth week
            $query = "WITH UserFirstWeek AS (
                    SELECT userid, 
                    STR_TO_DATE(DATE_FORMAT(FROM_UNIXTIME(MIN(created)), '%X-%V-1'), '%X-%V-%w') as first_week_date
                    FROM deepinsightusagelog
                    GROUP BY userid
                ),
                Activity AS (
                    SELECT DISTINCT 
                        u.userid,
                        u.first_week_date,
                        FLOOR((log.created - UNIX_TIMESTAMP(u.first_week_date)) / 604800) as week_number
                    FROM UserFirstWeek u
                    JOIN deepinsightusagelog log ON u.userid = log.userid
                    WHERE log.created BETWEEN ? AND ?
                    AND log.created >= UNIX_TIMESTAMP(u.first_week_date)
                ),
                CohortSizes AS (
                    SELECT first_week_date, COUNT(DISTINCT userid) as cohort_size
                    FROM UserFirstWeek
                    GROUP BY first_week_date
                )
                SELECT 
                    a.first_week_date,
                    a.week_number,
                    COUNT(DISTINCT a.userid) as active_users,
                    s.cohort_size
                FROM Activity a
                JOIN CohortSizes s ON a.first_week_date = s.first_week_date
                WHERE a.week_number >= 0 AND a.week_number < 8 -- Tracking up to 8 weeks
                GROUP BY a.first_week_date, a.week_number
                ORDER BY a.first_week_date ASC, a.week_number ASC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $result = $stmt->get_result();

            $rawStats = [];
            while ($row = $result->fetch_assoc()) 
            {
                $date = $row['first_week_date'];
                $weekNum = (int)$row['week_number'];
                $percent = ($row['active_users'] / $row['cohort_size']) * 100;
                
                $rawStats[$date][$weekNum] = round((float)$percent, 2);
            }

            $cohorts = [];
            foreach ($rawStats as $date => $weeks) 
            {
                // Ensure we have a consistent array (e.g., Week 0 to Week 7)
                $retentionArray = [];
                for ($i = 0; $i < 8; $i++) 
                {
                    $retentionArray[] = $weeks[$i] ?? 0.0;
                }

                $cohorts[] = new CohortRetention(
                    cohortName: "Week of " . $date,
                    weeklyRetention: $retentionArray
                );
            }

            return $cohorts;
        }

        //----------------------------------------------------------------------------------------

        public static function getActionUsageReport(Timespan $timespan): array
        {
            $db = (new DBConfig())->getConnection();

            $start = $timespan->start;
            $end = $timespan->stop;

            // 1. Get the total unique active users from the main usage log (the denominator)
            $totalUsersStmt = $db->prepare("SELECT COUNT(DISTINCT userid) FROM deepinsightusagelog WHERE created BETWEEN ? AND ?");
            $totalUsersStmt->bind_param("ii", $start, $end);
            $totalUsersStmt->execute();
            $totalActiveUsers = (int)($totalUsersStmt->get_result()->fetch_row()[0] ?? 1); // Avoid division by zero
            $totalUsersStmt->close();

            // 2. Get general stats per Action (Count and Unique Users for Conversion)
            $query = "SELECT 
                        name as action_name, 
                        COUNT(*) as total_count,
                        COUNT(DISTINCT userid) as unique_action_users
                    FROM deepinsightactionlog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY name";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $start, $end);
            $stmt->execute();
            $mainResult = $stmt->get_result();

            // 3. Get 7-Day Trend Data for all actions
            $trendData = AppUsage::getBulkAction7DayTrends($end);

            $actions = [];
            while ($row = $mainResult->fetch_assoc()) 
            {
                $name = $row['action_name'];
                $uniqueDoers = (int)$row['unique_action_users'];
                
                // Conversion Rate = (Users who did action / Total Active Users) * 100
                $conversion = ($uniqueDoers / $totalActiveUsers) * 100;

                $actions[] = new ActionUsage(
                    actionName: $name,
                    totalCount: (int)$row['total_count'],
                    conversionRate: round((float)$conversion, 2),
                    last7DaysTrend: $trendData[$name] ?? array_fill(0, 7, 0)
                );
            }

            return $actions;
        }

        /**
         * Helper to fetch the last 7 days of action counts
         */
        public static function getBulkAction7DayTrends($stopEpoch): array 
        {
            $db = (new DBConfig())->getConnection();

            $sevenDaysAgo = $stopEpoch - (7 * 86400);
            $trends = [];

            $query = "SELECT 
                        name, 
                        FROM_UNIXTIME(created, '%Y-%m-%d') as day_label, 
                        COUNT(*) as daily_count
                    FROM deepinsightactionlog 
                    WHERE created BETWEEN ? AND ?
                    GROUP BY name, day_label
                    ORDER BY day_label ASC";

            $stmt = $db->prepare($query);
            $stmt->bind_param("ii", $sevenDaysAgo, $stopEpoch);
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) 
            {
                $trends[$row['name']][] = (int)$row['daily_count'];
            }

            // Standardize to 7 elements
            foreach ($trends as $name => $data) 
            {
                if (count($data) < 7) 
                {
                    $trends[$name] = array_pad($data, -7, 0); 
                }
            }

            return $trends;
        }
    }