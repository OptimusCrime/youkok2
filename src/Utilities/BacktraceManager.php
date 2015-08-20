<?php
/*
 * File: BacktraceManager.php
 * Holds: Prettifies queries displayed in the footer in views
 * Created: 20.08.2015
 * Project: Youkok2
 */

namespace Youkok2\Utilities;

/*
 * The BacktraceManager class
 */

class BacktraceManager {
    
    /*
     * Clean up SQL log
     */
    
    public static function cleanSqlLog($arr) {
        // Some variables
        $str = '';
        $has_prepare = false;
        $prepare_val = [];
        
        // Check that we have some acutal queries here
        if (count($arr) > 0) {
            // Loop each post
            foreach ($arr as $v) {
                // Temp variables
                $temp_loc = $temp_loc = self::structureBacktrace($v['backtrace']);
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
                    $str .= $temp_loc . '<pre>' . $temp_query . '</pre>';
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
                return '<p>' . $trace['file'] . ' @ line ' . $trace['line'] . ':</p>';
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
                return '<p style="cursor: help;" title="' . $tooltip . '">' . $trace['file'] . ' @ line ' . $trace['line'] . ':</p>';
            }
        }
    }
} 