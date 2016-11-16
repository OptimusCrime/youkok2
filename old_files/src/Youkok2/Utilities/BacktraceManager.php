<?php
namespace Youkok2\Utilities;

use Youkok2\Utilities\Database;

class BacktraceManager
{
    
    private static $profilingInformation = [];
    
    public static function cleanSqlLog($arr) {
        $str = '';
        $has_prepare = false;
        $prepare_val = [];
        $profiling_index = 0;
        
        if (defined('PROFILING') and PROFILING) {
            self::$profilingInformation = Database::getProfilingData();
        }
        
        if (count($arr) > 0) {
            foreach ($arr as $k => $v) {
                $temp_loc = self::structureBacktrace($v['backtrace']);
                $temp_query = '';
                
                if (isset($v['query'])) {
                    // Normal query (no binds)
                    $temp_query = $v['query'];
                }
                elseif (isset($v['exec'])) {
                    // Normal exec (no binds)
                    $temp_query = $v['exec'];
                }
                else {
                    // Either bind or prepare
                    if (isset($v['prepare'])) {
                        $has_prepare = true;
                        $prepare_val = $v['prepare'];
                    }
                    elseif (isset($v['execute'])) {
                        if ($has_prepare) {
                            $temp_query = str_replace(array_keys($v['execute']), $v['execute'], $prepare_val);
                            
                            $has_prepare = false;
                        }
                    }
                }
                
                if (!$has_prepare) {
                    if (defined('PROFILING') and PROFILING) {
                        // Make sure to skip the first two queries
                        if ($profiling_index > 1) {
                            $temp_loc .= self::getProfilingInformation($profiling_index);
                        }
                        
                        $profiling_index++;
                    }
                    
                    $str .= $temp_loc . '</p><pre>' . htmlspecialchars($temp_query) . '</pre>';
                }
            }
        }
        
        return $str;
    }

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
                
                $line = '<p style="cursor: help;" title="' . $tooltip . '">';
                $line .= $trace['file'] . ' @ line ' . $trace['line'];
            }
            
            return $line;
        }
    }
    
    private static function getProfilingInformation($idx) {
        // Make sure the information exists (avoiding index notices)
        if (!isset(self::$profilingInformation[$idx - 2])) {
            return '';
        }
        
        return ' [' . round((self::$profilingInformation[$idx - 2]['Duration'] * 1000), 4) . ' ms]';
    }
}
