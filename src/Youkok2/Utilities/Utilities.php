<?php
/*
 * File: utilities.php
 * Holds: Different minor functions
 * Created: 23.06.14
 * Project: Youkok2
 * 
 */

namespace Youkok2\Utilities;

class Utilities
{
    
    /*
     * Prettify SQL dates
     */
    
    public static function prettifySQLDate($d, $include_time = true) {
        $norwegian_months = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep',
                                 'okt', 'nov', 'des'];
        
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        return (int) $split2[2] . '. ' . $norwegian_months[$split2[1] - 1] . ' ' . $split2[0] .
            ($include_time ? (' @ ' . $split1[1]) : '');
    }

    /*
     * Generate URL friendly
     */

    public static function urlSafe($s) {
        // Replace first here to keep "norwegian" names in a way
        $s = str_replace(['Æ', 'Ø', 'Å'], ['ae', 'o', 'aa'], $s);
        $s = str_replace(['æ', 'ø', 'å'], ['ae', 'o', 'aa'], $s);
        
        // Replace multiple spaces to dashes
        $s = preg_replace('!\s+!', '-', $s);
        
        // Final replace replace
        $s = preg_replace('![^-_a-z0-9\s\.]+!', '', strtolower($s));
        
        // Return here
        return $s;
    }

    /*
     * Hash the passwords
     */

    public static function hashPassword($pass, $salt, $hard = true) {
        // Create hash
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12, 'salt' => $salt]);

        // Check if the hash should be fucked up in addition
        if ($hard) {
            return self::passwordFuckup($hash);
        }
        else {
            return $hash;
        }
    }
    
    /*
     * Change up the password a bit
     */

    public static function passwordFuckup($s) {
        // Split the hash
        $splits = [substr($s, 0, 10),
            substr($s, 10, 10),
            substr($s, 20)];

        // Rejoin and full with stuff
        return $splits[0] . 'kebab' . $splits[2] . '6071f11238e773ac6bb269ae0a0d4f4bhslee' . $splits[1] . 'yolo';
    }

    /*
     * Change the password back to the initial state
     */

    public static function reverseFuckup($s) {
        // Split the hash
        $splits = [substr($s, 0, 10),
            substr($s, 15, 40),
            substr($s, 92, 10)];

        // Return the correct
        return $splits[0] . $splits[2] . $splits[1];
    }

    /*
     * Generate random salt
     */

    public static function generateSalt() {
        return md5(rand(0, 10000000000)) . "-" . md5(time()) . "dl7fTxPQkzSfMiCqY704aetj9Se2jfYURBAySs8y";
    }
    
    /*
     * Prettify file size
     */
    
    public static function prettifyFilesize($bytes) {
        if ($bytes > 0) {
            $unit = intval(log($bytes, 1024));
            $units = ['B', 'kB', 'MB', 'GB'];

            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
        
        // Return pretty filesize
        return $bytes;
    }
}
