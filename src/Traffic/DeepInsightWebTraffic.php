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
    // Days and months constants for better maintainability
    private const DAYS_OF_WEEK = ['sun', 'mon', 'tue', 'wed', 'thu', 'fri', 'sat'];
    private const MONTHS = ['jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec'];
    
    private const SEARCH_ENGINES = ['google', 'bing', 'yahoo'];
    private const AI_PLATFORMS = ['chatgpt', 'gemini', 'claud', 'qwen'];
    
    // User intent thresholds
    private const INTENT_LOW_MAX_PAGES = 1;
    private const INTENT_LOW_MAX_DURATION = 30;
    private const INTENT_MEDIUM_MAX_PAGES = 3;
    private const INTENT_MEDIUM_MAX_DURATION = 120;

    public function __construct(
        public int $totalVisits,
        public int $botSessions,
        public int $pagesPerSession,
        public float $bounceRate,
        public int $avgDuration,
        public int $newVisits,
        public int $returingVisits,
        public UserIntent $userIntent,
        public array $referrer = [],
        public VisitChannel $channel,
        public array $source = [],
        public array $browser = [],
        public array $devices = [],
        public array $os = [],
        public array $region = [],
        public array $pageRanking = [],
        public array $weeklyVisits = [],
        public array $monthlyVisits = [],
        public array $visitTrend = [],
    ){}

    public static function FromLogs(Timespan $timeSpan): DeepInsightWebTraffic
    {
        $logs = DeepInsightVisit::Get(new Filter(['created' => $timeSpan, 'isbot' => false]))->list;
        $frontLogs = FrontEndTrafficLog::Get(new Filter(['created' => $timeSpan]))->list;

        // Process all metrics in single pass
        $metrics = self::processLogsInSinglePass($logs, $timeSpan);
        
        // Process front-end logs for page ranking
        $pageRanking = self::calculatePageRanking($frontLogs);

        return new DeepInsightWebTraffic(
            DeepInsightVisit::Count(new Filter(['isbot' => false, 'created' => $timeSpan])),
            DeepInsightVisit::Count(new Filter(['isbot' => true, 'created' => $timeSpan])),
            self::pageVisitPerSession($timeSpan),
            self::bounceRate($timeSpan),
            self::avgDuration($timeSpan),
            DeepInsightVisit::Count(new Filter(['isrevisit' => false, 'isbot' => false, 'created' => $timeSpan])),
            DeepInsightVisit::Count(new Filter(['isrevisit' => true, 'isbot' => false, 'created' => $timeSpan])),
            self::getUserIntent($timeSpan),
            $metrics['referrer'],
            $metrics['channels'],
            $metrics['sources'],
            $metrics['browsers'],
            $metrics['devices'],
            $metrics['os'],
            $metrics['regions'],
            $pageRanking,
            $metrics['weeklyTrend'],
            $metrics['monthlyTrend'],
            $metrics['visitTrend'],
        );
    }

    /**
     * Process all log metrics in a single iteration
     */
    private static function processLogsInSinglePass($logs, Timespan $timeSpan): array
    {
        $timeSplit = $timeSpan->splitSpan(10);
        
        // Initialize associative arrays
        $aggregates = [
            'os' => [],
            'browser' => [],
            'devices' => [],
            'region' => [],
            'referrer' => [],
            'sources' => [],
            'visitTrend' => array_fill_keys(array_column($timeSplit, 'start'), 0),
        ];

        // Initialize weekly and monthly trends
        $weeklyTrend = self::initializeNamedArray(self::DAYS_OF_WEEK);
        $monthlyTrend = self::initializeNamedArray(self::MONTHS);

        // Channel counters
        $channels = new VisitChannel(0, 0, 0, 0, 0);

        // Single pass through logs
        foreach ($logs as $log) {
            $timestamp = $log->created->toEpochSeconds();
            $refererLower = strtolower($log->referer);
            
            // Aggregate data
            self::incrementAssoc($aggregates['os'], $log->os ?: 'Others');
            self::incrementAssoc($aggregates['browser'], $log->browser ?: 'Others');
            self::incrementAssoc($aggregates['devices'], $log->deviceName ?: 'Others');
            self::incrementAssoc($aggregates['region'], $log->country ?: 'Others');
            self::incrementAssoc($aggregates['referrer'], $log->referer ?: 'Others');

            // Process channels and sources
            self::processChannelAndSource($refererLower, $channels, $aggregates['sources']);

            // Weekly trend
            $dayOfWeek = strtolower(date("D", $timestamp));
            self::incrementArrayByName($weeklyTrend, $dayOfWeek);

            // Monthly trend
            $month = strtolower(date("M", $timestamp));
            self::incrementArrayByName($monthlyTrend, $month);

            // Visit trend (time-based)
            foreach ($timeSplit as $split) {
                if ($timestamp >= $split->start && $timestamp <= $split->stop) {
                    $aggregates['visitTrend'][$split->start]++;
                    break;
                }
            }
        }

        return [
            'os' => self::convertToNameValuePairs($aggregates['os']),
            'browsers' => self::convertToNameValuePairs($aggregates['browser']),
            'devices' => self::convertToNameValuePairs($aggregates['devices']),
            'regions' => self::convertToNameValuePairs($aggregates['region']),
            'referrer' => self::convertToNameValuePairs($aggregates['referrer']),
            'sources' => self::convertToNameValuePairs($aggregates['sources']),
            'weeklyTrend' => $weeklyTrend,
            'monthlyTrend' => $monthlyTrend,
            'visitTrend' => self::convertVisitTrendToNameValuePairs($aggregates['visitTrend']),
            'channels' => $channels,
        ];
    }

    /**
     * Process channel classification and source tracking
     */
    private static function processChannelAndSource(string $refererLower, VisitChannel $channels, array &$sources): void
    {
        $isOrganic = empty(trim($refererLower)) || 
                     strpos($refererLower, $_SERVER['HTTP_HOST'] ?? '') !== false ||
                     strpos($refererLower, DeepInsightEnv::getHostName()) !== false;

        if ($isOrganic) {
            $channels->organic++;
            self::incrementAssoc($sources, 'organic');
            return;
        }

        // Check search engines
        foreach (self::SEARCH_ENGINES as $engine) {
            if (strpos($refererLower, $engine) !== false) {
                $channels->searchEngines++;
                self::incrementAssoc($sources, $engine);
                return;
            }
        }

        // Check AI platforms
        foreach (self::AI_PLATFORMS as $platform) {
            if (strpos($refererLower, $platform) !== false) {
                $channels->aiPlatform++;
                self::incrementAssoc($sources, $platform);
                return;
            }
        }

        // Check social media
        if (strpos($refererLower, 'facebook') !== false) {
            self::incrementAssoc($sources, 'facebook');
        }

        $channels->others++;
    }

    /**
     * Calculate page ranking from front-end logs
     */
    private static function calculatePageRanking($frontLogs): array
    {
        $pageRank = [];
        
        foreach ($frontLogs as $log) {
            self::incrementAssoc($pageRank, $log->page);
        }

        return self::convertToNameValuePairs($pageRank);
    }

    /**
     * Helper: Initialize named array with zero values
     */
    private static function initializeNamedArray(array $names): array
    {
        return array_map(fn($name) => new NameValuePair($name, 0), $names);
    }

    /**
     * Helper: Increment associative array value
     */
    private static function incrementAssoc(array &$array, string $key): void
    {
        $array[$key] = ($array[$key] ?? 0) + 1;
    }

    /**
     * Helper: Increment NameValuePair array by name
     */
    private static function incrementArrayByName(array &$array, string $name): void
    {
        foreach ($array as $item) {
            if ($item->name === $name) {
                $item->value++;
                break;
            }
        }
    }

    /**
     * Helper: Convert associative array to NameValuePair array
     */
    private static function convertToNameValuePairs(array $assoc): array
    {
        return array_map(
            fn($key, $value) => new NameValuePair($key, $value),
            array_keys($assoc),
            array_values($assoc)
        );
    }

    /**
     * Helper: Convert visit trend to NameValuePair with formatted dates
     */
    private static function convertVisitTrendToNameValuePairs(array $visitTrend): array
    {
        return array_map(
            fn($timestamp, $count) => new NameValuePair(date("d M", (int)$timestamp), $count),
            array_keys($visitTrend),
            array_values($visitTrend)
        );
    }

    public static function avgDuration(Timespan $timeSpan): float
    {
        $mysqli = (new DBConfig())->getConnection();

        $stmt = $mysqli->prepare(
            "SELECT AVG(session_duration) AS avg_duration_per_session
            FROM (
                SELECT sessionid, SUM(duration) AS session_duration
                FROM frontendtrafficlog
                WHERE created BETWEEN ? AND ?
                GROUP BY sessionid
            ) t"
        );

        if (!$stmt) {
            throw new \Exception($mysqli->error);
        }

        $stmt->bind_param('ss', $timeSpan->start, $timeSpan->stop);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (float) ($row['avg_duration_per_session'] ?? 0);
    }

    public static function bounceRate(Timespan $timeSpan): float
    {
        $mysqli = (new DBConfig())->getConnection();

        $stmt = $mysqli->prepare(
            "SELECT 
                COUNT(*) AS total_sessions, 
                SUM(CASE WHEN page_count = 1 THEN 1 ELSE 0 END) AS single_page_sessions
            FROM (
                SELECT sessionid, COUNT(*) AS page_count
                FROM frontendtrafficlog
                WHERE created BETWEEN ? AND ?
                GROUP BY sessionid
            ) t"
        );

        if (!$stmt) {
            throw new \Exception($mysqli->error);
        }

        $stmt->bind_param('ss', $timeSpan->start, $timeSpan->stop);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['total_sessions'] == 0) {
            return 0.0;
        }

        return ($row['single_page_sessions'] / $row['total_sessions']) * 100;
    }

    public static function pageVisitPerSession(Timespan $timeSpan): float
    {
        $mysqli = (new DBConfig())->getConnection();

        $stmt = $mysqli->prepare(
            "SELECT AVG(page_count) AS avg_pages_per_session
            FROM (
                SELECT sessionid, COUNT(*) AS page_count
                FROM frontendtrafficlog
                WHERE created BETWEEN ? AND ?
                GROUP BY sessionid
            ) t"
        );

        if (!$stmt) {
            throw new \Exception($mysqli->error);
        }

        $stmt->bind_param('ss', $timeSpan->start, $timeSpan->stop);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (float) ($row['avg_pages_per_session'] ?? 0);
    }

    public static function getUserIntent(Timespan $timeSpan): UserIntent
    {
        $mysqli = (new DBConfig())->getConnection();

        $stmt = $mysqli->prepare(
            "SELECT 
                sessionid,
                COUNT(*) AS pages_visited,
                SUM(duration) AS total_duration,
                CASE 
                    WHEN COUNT(*) = 1 OR SUM(duration) < ? THEN 'low'
                    WHEN COUNT(*) <= ? AND SUM(duration) < ? THEN 'medium'
                    ELSE 'high'
                END AS intent
            FROM frontendtrafficlog
            WHERE created BETWEEN ? AND ?
            GROUP BY sessionid"
        );

        if (!$stmt) {
            throw new \Exception($mysqli->error);
        }

        $lowDuration = self::INTENT_LOW_MAX_DURATION;
        $mediumPages = self::INTENT_MEDIUM_MAX_PAGES;
        $mediumDuration = self::INTENT_MEDIUM_MAX_DURATION;

        $stmt->bind_param('iiiss', $lowDuration, $mediumPages, $mediumDuration, $timeSpan->start, $timeSpan->stop);
        $stmt->execute();

        $result = $stmt->get_result();
        
        $intentCounts = ['high' => 0, 'medium' => 0, 'low' => 0];
        
        while ($row = $result->fetch_assoc()) {
            $intentCounts[$row['intent']]++;
        }
        
        $stmt->close();

        $total = array_sum($intentCounts);
        
        $userIntent = new UserIntent(0, 0, 0);
        
        if ($total > 0) {
            $userIntent->high = ($intentCounts['high'] / $total) * 100;
            $userIntent->medium = ($intentCounts['medium'] / $total) * 100;
            $userIntent->low = ($intentCounts['low'] / $total) * 100;
        }

        return $userIntent;
    }
}