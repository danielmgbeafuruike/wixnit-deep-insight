<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    use Wixnit\Data\DBConfig;
    use Wixnit\Utilities\Timespan;

    class UsersBehaviour
    {
        public function __construct(
            public BehaviorSummary $summary,

            /**
             * @param BehaviorHour[] $hourly
             */
            public array $hourly = [],

            /**
             * @param MilestoneReturn[] $milestones
             */
            public array $milestones = [],

            /**
             * @param RetentionPoint[] $retention
             */
            public array $retention = [],

            /**
             * @param  SessionPeriod[] $periods
             */
            public array $periods = [],

            /**
             * @param UserFlow $flows
             */
            public array $flows = [],
        ){}


        public static function FromLogs(Timespan $timespan): UsersBehaviour
        {
            return new UsersBehaviour(
                new BehaviorSummary(
                    0,
                    0,
                    0,
                    0,
                ),
                [

                ],
                [
                    
                ],
                [
                    
                ],
                [
                    
                ],
                [
                    
                ],
            );
        }



        /**
         * 1. Next Day Return Rate (Day 1 Retention)
         * Percentage of users who used the app on Day 0 and returned on Day 1.
         */
        public static function getNextDayReturnRate(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();
            
            $startDate= $timespan->start;
            $endDate = $timespan->stop;


            $query = "SELECT 
                    COUNT(DISTINCT day0.userid) as initial_users,
                    COUNT(DISTINCT day1.userid) as returning_users
                FROM (
                    SELECT userid, DATE(FROM_UNIXTIME(created)) as activity_date 
                    FROM deepinsightusagelogid 
                    WHERE FROM_UNIXTIME(created) BETWEEN ? AND ?
                ) day0
                LEFT JOIN deepinsightusagelogid day1 ON day0.userid = day1.userid 
                    AND DATE(FROM_UNIXTIME(day1.created)) = DATE_ADD(day0.activity_date, INTERVAL 1 DAY)
            ";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            return $result['initial_users'] > 0 
                ? ($result['returning_users'] / $result['initial_users']) * 100 
                : 0;
        }

        /**
         * 2. Retention 7 Day
         * Users who were active on Day 0 and were active again exactly 7 days later.
         */
        public static function getRetention7Day(Timespan $timespan) 
        {
            $db = (new DBConfig())->getConnection();
            
            $startDate= $timespan->start;
            $endDate = $timespan->stop;


            $query = "SELECT 
                    COUNT(DISTINCT day0.userid) as initial_users,
                    COUNT(DISTINCT day7.userid) as returning_users
                FROM (
                    SELECT userid, DATE(FROM_UNIXTIME(created)) as activity_date 
                    FROM deepinsightusagelogid 
                    WHERE FROM_UNIXTIME(created) BETWEEN ? AND ?
                ) day0
                LEFT JOIN deepinsightusagelogid day7 ON day0.userid = day7.userid 
                    AND DATE(FROM_UNIXTIME(day7.created)) = DATE_ADD(day0.activity_date, INTERVAL 7 DAY)
            ";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ss", $startDate, $endDate);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            return $result['initial_users'] > 0 
                ? ($result['returning_users'] / $result['initial_users']) * 100 
                : 0;
        }

        /**
         * 3. Avg Time To First Action (in minutes)
         * Time between a user's very first log (navindex 0) and their first target action.
         * Assuming 'action_screen' is the screen name where the "action" happens.
         */
        public static function getAvgTimeToFirstAction($actionScreen) 
        {
            $db = (new DBConfig())->getConnection();
            
            $query = "SELECT AVG(first_action.created - first_seen.created) / 60 as avg_minutes
                FROM (
                    SELECT userid, MIN(created) as created 
                    FROM deepinsightusagelogid 
                    WHERE navindex = 0 GROUP BY userid
                ) first_seen
                JOIN (
                    SELECT userid, MIN(created) as created 
                    FROM deepinsightusagelogid 
                    WHERE screen = ? GROUP BY userid
                ) first_action ON first_seen.userid = first_action.userid
                WHERE first_action.created >= first_seen.created
            ";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("s", $actionScreen);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            return (float)$result['avg_minutes'];
        }

        /**
         * 4. Milestone Completion Rate
         * The percentage of users who reached a 'target' screen vs those who started.
         */
        public static function getMilestoneCompletionRate($startScreen, $milestoneScreen) 
        {
            $db = (new DBConfig())->getConnection();
            
            $query = "SELECT 
                    COUNT(DISTINCT CASE WHEN screen = ? THEN userid END) as started,
                    COUNT(DISTINCT CASE WHEN screen = ? THEN userid END) as completed
                FROM deepinsightusagelogid
            ";
            
            $stmt = $db->prepare($query);
            $stmt->bind_param("ss", $startScreen, $milestoneScreen);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            return $result['started'] > 0 
                ? ($result['completed'] / $result['started']) * 100 
                : 0;
        }
    }