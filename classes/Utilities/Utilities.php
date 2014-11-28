<?php
/*
 * File: utilities.php
 * Holds: Different minor functions
 * Created: 23.06.14
 * Project: Youkok2
 * 
*/

namespace Youkok2\Utilities;

class Utilities {

    /*
     * Prettify SQL dates
     */
    
    public static function prettifySQLDate($d, $include_time = true) {
        $norwegian_months = array('jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 
                                 'okt', 'nov', 'des');
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);
        
        return (int) $split2[2] . '. ' . $norwegian_months[$split2[1] - 1] . ' ' . $split2[0] . ($include_time ? (' @ ' . $split1[1]) : '');
    }

    /*
     * Generate URL friendly
     */

    public static function generateUrlFriendly($s, $for_url = false) {
        // Replace first here to keep "norwegian" names in a way
        $s = str_replace(array('Æ', 'Ø', 'Å'), array('ae', 'o', 'aa'), $s);
        $s = str_replace(array('æ', 'ø', 'å'), array('ae', 'o', 'aa'), $s);
        
        // Replace multiple spaces with a single spaces
        $s = preg_replace('!\s+!', ' ', $s);
        
        // Decide how to deal with spaces
        if ($for_url) {
            $s = str_replace(' ', '-', $s);
        }
        else {
            $s = str_replace(' ', '_', $s);
        }

        $s = preg_replace('![^-_a-z0-9\s\.]+!', '', strtolower($s));
        
        return $s;
    }
    
    /*
     * Change up the password a bit
     */

    public static function passwordFuckup($s) {
        // Split the hash
        $splits = array(substr($s, 0, 5),
            substr($s, 5, 5),
            substr($s, 10, 10),
            substr($s, 20));

        // Rejoin and full with stuff
        return $splits[0] . $splits[1] . 'kebab' . $splits[3] . md5('ingrid vold') . 'hslee' . $splits[2] . 'yolo';
    }
    
    /*
     * Prettify file size
     */
    
    public static function prettifyFilesize($z) {
        $bytes = $z;
        if ($bytes > 0) {
            $unit = intval(log($bytes, 1024));
            $units = array('B', 'kB', 'MB', 'GB');

            if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
            }
        }
        return $bytes;
    }
}