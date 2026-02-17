<?php

    namespace Wixnit\DeepInsight\Resources;

    use Wixnit\App\Container;
    use Wixnit\App\Model;

    class DeepInsightRUL extends Model
    {
        public string $route;
        public float $ramUsed;
        public float $cpuLoad;
        public float $processingTime;
        public float $diskIO;



        public static function initLog()
        {
            $log = [
                'ramUsed' => memory_get_usage(),
                'processingTime' => microtime(true),
                'startUsage' => getrusage(),
            ];

            Container::set('deepInsightResourceUsageLog',$log);
        }
        public static function buildLog(string $route): DeepInsightRUL | null
        {
            if(Container::has("deepInsightResourceUsageLog"))
            {
                $endUsage = getrusage();

                //CPU Usage
                $cpuStart = Container::get('deepInsightResourceUsageLog')['startUsage']["ru_utime.tv_sec"] + (Container::get('deepInsightResourceUsageLog')['startUsage']["ru_utime.tv_usec"] / 1000000);
                $cpuEnd = $endUsage["ru_utime.tv_sec"] + ($endUsage["ru_utime.tv_usec"] / 1000000);
                $cpuUsed = round($cpuEnd - $cpuStart, 4);

                //Disk I/O (Inputs and Outputs)
                $diskReads = $endUsage["ru_inblock"] - Container::get('deepInsightResourceUsageLog')['startUsage']["ru_inblock"];
                $diskWrites = $endUsage["ru_oublock"] - Container::get('deepInsightResourceUsageLog')['startUsage']["ru_oublock"];


                $log = new DeepInsightRUL();
                $log->route = $route;
                $log->processingTime = round((microtime(true) - Container::get('deepInsightResourceUsageLog')['processingTime']), 4);
                $log->ramUsed = round(((memory_get_usage() - Container::get('deepInsightResourceUsageLog')['ramUsed']) / 1024 / 1024), 4);
                $log->cpuLoad = $cpuUsed;
                $log->diskIO = ($diskReads + $diskWrites) * 512;
                return $log;
            }
            return null;
        }
    }