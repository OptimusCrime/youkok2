<?php
/*
 * File: BacktraceManager.php
 * Holds: Prettifies queries displayed in the footer in views
 * Created: 20.08.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

use \Youkok2\Utilities\Database as Database;

class BacktraceManager {
    
    /*
     * Various variables
     */
    
    private static $profilingInformation = [];
    
    /*
     * Clean up SQL log
     */
    
    public static function cleanSqlLog($arr) {
        // Some variables
        $str = '';
        $has_prepare = false;
        $prepare_val = [];
        $profiling_index = 0;
        
        // Check if we are profiling
        if (defined('PROFILING') and PROFILING) {
            self::$profilingInformation = Database::getProfilingData();
        }
        
        // Check that we have some acutal queries here
        if (count($arr) > 0) {
            // Loop each post
            foreach ($arr as $k => $v) {
                // Temp variables
                $temp_loc = self::structureBacktrace($v['backtrace']);
                $temp_query = '';
                
                // Check what kind of query we're dealing with
                if (isset($v['query'])) {
                    // Normal query (no binds)
                    $temp_query = $v['query'];
                }
                else if (isset($v['exec'])) {
                    // Normal exec (no binds)
                    $temp_query = $v['exec'];
                }
                else {
                    // Either bind or prepare
                    if (isset($v['prepare'])) {
                        // Query is being preared
                        $has_prepare = true;
                        $prepare_val = $v['prepare'];
                    }
                    else if (isset($v['execute'])) {
                        // Query is executed with binds, check if binds are found
                        if ($has_prepare) {
                            // Binds are found, replace keys with bind values
                            $temp_query = str_replace(array_keys($v['execute']), $v['execute'], $prepare_val);
                            
                            // Reset prepare-value
                            $has_prepare = false;
                        }
                    }
                }
                
                // Clean up n stuff
                if (!$has_prepare) {
                    // Check if we should add the profiling timestamps
                    if (defined('PROFILING') and PROFILING) {
                        // Make sure to skip the first two queries
                        if ($profiling_index > 1) {
                            $temp_loc .= self::getProfilingInformation($profiling_index);
                        }
                        
                        // Increase profiling index
                        $profiling_index++;
                    }
                    
                    // Apply the final string
                    $str .= $temp_loc . '</p><pre>' . htmlspecialchars($temp_query) . '</pre>';
                }
            }
        }
        
        // Return resulting string
        return $str;
    }
    
    /*
     * Structures the backtraces
     */
    
    private static function structureBacktrace($arr) {
        if (count($arr) > 0) {
            $trace = $arr[0];
            if (count($arr) == 1) {
                $line = '<p>' . $trace['file'] . ' @ line ' . $trace['line'];
            }
            else {
                $tooltip = '';
                $lim = ((count($arr) > 15) ? 14 : (count($arr) - 1));
                
                for ($i = 1; $i <= $lim; $i++) {
                    $trace_temp = $arr[$i];
                    if (isset($trace_temp['file'])) {
                        $tooltip .= ($i + 1) . '. ' . $trace_temp['file'] . ' @ line ' . $trace_temp['line'] . "&#xA;";
                    }
                }
                
                $line = '<p style="cursor: help;" title="' . $tooltip . '">' . $trace['file'] . ' @ line ' . $trace['line'];
            }
            
            // Return the final line
            return $line;
        }
    }
    
    /*
     * Returns the profiling execution time
     */
    
    private static function getProfilingInformation($idx) {
        return ' [' . round((self::$profilingInformation[$idx - 2]['Duration'] * 1000), 4) . ' ms]';
    }
} 