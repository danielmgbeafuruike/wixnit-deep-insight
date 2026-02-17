<?php

    namespace Wixnit\DeepInsight;

    class DeepInsightEnv 
    {
        public static function getHostName(): string
        {
            return gethostname() ?: (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'Unknown'); //php_uname('n');
        }
        
        public static function getOS(): string
        {
            return php_uname('s');
        }
        public static function getOSVersion(): string
        {
            return php_uname('r');
        }

        public static function getPHPVersion(): string
        {
            return phpversion();
        }

        public static function getPHPInterface(): string
        {
            return php_sapi_name();
        }

        public static function getRegion(): string
        {
            return date_default_timezone_get() ?: 'Unknown';
        }

        public static function getMemoryLimit(): string
        {
            return ini_get('memory_limit');
        }

        public static function getMemoryUsage(): string
        {
            return memory_get_usage();
        }

        public static function getMemoryPeak(): string
        {
            return memory_get_peak_usage();
        }

        public static function getArchitecture(): string
        {
            return php_uname('m');
        }

        public static function getCPUCount(): string
        {
            return shell_exec('nproc');
        }

        public static function getCPUUsage(): string
        {
            return sys_getloadavg()[0];
        }
        public static function getCPUUsagePercentage(): string
        {
            return sys_getloadavg()[0] / self::getCPUCount() * 100;
        }

        public static function getCPUUsagePercentageString(): string
        {
            return self::getCPUUsagePercentage() . "%";
        }


        public static function getRAMSize(): string
        {
            return shell_exec('free -m | grep Mem | awk \'{print $2}\'');
        }

        public static function getRAMSizeString(): string
        {
            return self::getRAMSize() . "MB";
        }

        public static function getRAMUsage(): string
        {
            return shell_exec('free -m | grep Mem | awk \'{print $3}\'');
        }

        public static function getRAMUsagePercentage(): string
        {
            return self::getRAMUsage() / self::getRAMSize() * 100;
        }

        public static function getRAMUsagePercentageString(): string
        {
            return self::getRAMUsagePercentage() . "%";
        }

        public static function getRAMUsageString(): string
        {
            return self::getRAMUsage() . "MB";
        }

        public static function getDisk(): string
        {
            return shell_exec('df -h | grep /dev/sda1 | awk \'{print $1}\'');
        }

        public static function getDiskUsage(): string
        {
            return shell_exec('df -h | grep /dev/sda1 | awk \'{print $3}\'');
        }

        public static function getDiskUsagePercentage(): string
        {
            return shell_exec('df -h | grep /dev/sda1 | awk \'{print $5}\'');
        }

        public static function getDiskUsagePercentageString(): string
        {
            return self::getDiskUsagePercentage() . "%";
        }

        public static function getDiskUsageString(): string
        {
            return self::getDiskUsage() . "GB";
        }

        public static function getDiskSize(): string
        {
            // Get disk information for the current filesystem
            $path = __DIR__; // Current directory path

            if (function_exists('disk_total_space') && function_exists('disk_free_space')) 
            {
                return disk_total_space($path);
                //$free  = disk_free_space($path);
            } 
            else 
            {
                
                return "Not available (disk_total_space disabled)";
            }

            // Optional: Get information for all mounted filesystems (Linux/Unix only)
            /*
            if (PHP_OS_FAMILY !== 'Windows' && function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions') ?: '')))) 
            {
                echo "\n=== All Filesystems ===\n";
                $output = [];
                exec('df -h 2>/dev/null', $output, $return_code);
                
                if ($return_code === 0 && count($output) > 1) 
                {
                    // Skip header line
                    array_shift($output);
                    foreach ($output as $line) 
                    {
                        // Clean up extra spaces and split
                        $parts = preg_split('/\s+/', trim($line));
                        if (count($parts) >= 6) 
                        {
                            printf("%-20s %8s %8s %8s %8s %s\n",
                                $parts[0],  // Filesystem
                                $parts[1],  // Size
                                $parts[2],  // Used
                                $parts[3],  // Available
                                $parts[4],  // Use%
                                $parts[5]   // Mounted on
                            );
                        }
                    }
                } 
                else 
                {
                    echo "Detailed filesystem info not available\n";
                }
            }
            */
        }

        public static function getFreeDiskSize(): string
        {
            // Get disk information for the current filesystem
            $path = __DIR__; // Current directory path

            if (function_exists('disk_total_space') && function_exists('disk_free_space')) 
            {
                return disk_free_space($path);
            } 
            else 
            {
                return "Not available (disk_free_space disabled)\n";
            }

            // Optional: Get information for all mounted filesystems (Linux/Unix only)
            /*
            if (PHP_OS_FAMILY !== 'Windows' && function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions') ?: '')))) 
            {
                echo "\n=== All Filesystems ===\n";
                $output = [];
                exec('df -h 2>/dev/null', $output, $return_code);
                
                if ($return_code === 0 && count($output) > 1) 
                {
                    // Skip header line
                    array_shift($output);
                    foreach ($output as $line) 
                    {
                        // Clean up extra spaces and split
                        $parts = preg_split('/\s+/', trim($line));
                        if (count($parts) >= 6) 
                        {
                            printf("%-20s %8s %8s %8s %8s %s\n",
                                $parts[0],  // Filesystem
                                $parts[1],  // Size
                                $parts[2],  // Used
                                $parts[3],  // Available
                                $parts[4],  // Use%
                                $parts[5]   // Mounted on
                            );
                        }
                    }
                } 
                else 
                {
                    echo "Detailed filesystem info not available\n";
                }
            }
            */
        }


        public static function getUsedDiskSize(): string
        {
            // Get disk information for the current filesystem
            $path = __DIR__; // Current directory path

            if (function_exists('disk_total_space') && function_exists('disk_free_space')) 
            {
                $total = disk_total_space($path);
                $free  = disk_free_space($path);
                $used  = $total - $free;
                
                return round(($used / $total) * 100, 2);
            } 
            else 
            {
                return "Not available (disk_free_space disabled)";
            }

            // Optional: Get information for all mounted filesystems (Linux/Unix only)
            /*
            if (PHP_OS_FAMILY !== 'Windows' && function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions') ?: '')))) 
            {
                echo "\n=== All Filesystems ===\n";
                $output = [];
                exec('df -h 2>/dev/null', $output, $return_code);
                
                if ($return_code === 0 && count($output) > 1) 
                {
                    // Skip header line
                    array_shift($output);
                    foreach ($output as $line) {
                        // Clean up extra spaces and split
                        $parts = preg_split('/\s+/', trim($line));
                        if (count($parts) >= 6) {
                            printf("%-20s %8s %8s %8s %8s %s\n",
                                $parts[0],  // Filesystem
                                $parts[1],  // Size
                                $parts[2],  // Used
                                $parts[3],  // Available
                                $parts[4],  // Use%
                                $parts[5]   // Mounted on
                            );
                        }
                    }
                } 
                else 
                {
                    echo "Detailed filesystem info not available\n";
                }
            }
            */
        }

        public static function getCPU(): string
        {
            return shell_exec('cat /proc/cpuinfo | grep "model name" | uniq | awk -F: \'{print $2}\'');
        }

        public static function getCPUCountString(): string
        {
            return self::getCPUCount() . " cores";
        }

        public static function getTimezone(): string
        {
            return date_default_timezone_get();
        }

        public static function getUptime(): string
        {
            return shell_exec('uptime -p');
        }


        public static function ram(): string
        {
            $memory = "Unknown";

            $mem_info = self::safe_exec('cat /proc/meminfo 2>/dev/null');
            if ($mem_info && preg_match('/MemTotal:\s+(\d+)\s+kB/i', $mem_info, $matches)) {
                $memory = self::format_bytes($matches[1] * 1024);
            }

            // Fallback to PHP memory limit if physical memory unavailable
            if ($memory === 'Unknown') {
                $memory = ini_get('memory_limit') ?: 'Unlimited';
            }
            return $memory;
        }

        public static function safe_exec($command) {
            if (function_exists('exec') && !in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                $output = null;
                exec($command, $output, $return_code);
                return $return_code === 0 ? trim(implode(' ', $output)) : null;
            }
            return null;
        }

        // Helper function to convert bytes to human-readable format
        public static function format_bytes($size, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
                $size /= 1024;
            }
            return round($size, $precision) . ' ' . $units[$i];
        }
    }