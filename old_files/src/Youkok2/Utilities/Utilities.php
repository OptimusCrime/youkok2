<?php
namespace Youkok2\Utilities;

class Utilities
{
    
    public static function prettifySQLDate($d, $include_time = true) {
        if ($d == 'CURRENT_TIMESTAMP') {
            $d = date('Y-m-d  G:i:s');
        }

        $norwegian_months = ['jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep',
                                 'okt', 'nov', 'des'];
        
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);

        return (int) $split2[2] . '. ' . $norwegian_months[$split2[1] - 1] . ' ' . $split2[0] .
            ($include_time ? (' @ ' . $split1[1]) : '');
    }

    public static function urlSafe($s) {
        // Replace first here to keep "norwegian" names in a way
        $s = str_replace(['Æ', 'Ø', 'Å'], ['ae', 'o', 'aa'], $s);
        $s = str_replace(['æ', 'ø', 'å'], ['ae', 'o', 'aa'], $s);
        
        // Replace multiple spaces to dashes and remove special chars
        $s = preg_replace('!\s+!', '-', $s);
        $s = preg_replace('![^-_a-z0-9\s\.]+!', '', strtolower($s));
        
        return $s;
    }

    public static function hashPassword($pass, $salt, $hard = true) {
        $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12, 'salt' => $salt]);

        if ($hard) {
            return self::passwordFuckup($hash);
        }
        else {
            return $hash;
        }
    }

    public static function passwordFuckup($s) {
        $splits = [substr($s, 0, 10),
            substr($s, 10, 10),
            substr($s, 20)];

        return $splits[0] . 'kebab' . $splits[2] . '6071f11238e773ac6bb269ae0a0d4f4bhslee' . $splits[1] . 'yolo';
    }

    public static function reverseFuckup($s) {
        $splits = [substr($s, 0, 10),
            substr($s, 15, 40),
            substr($s, 92, 10)];

        return $splits[0] . $splits[2] . $splits[1];
    }

    public static function generateSalt() {
        return md5(rand(0, 10000000000)) . "-" . md5(time()) . "dl7fTxPQkzSfMiCqY704aetj9Se2jfYURBAySs8y";
    }
    
    public static function prettifyFilesize($bytes) {
        if ($bytes > 0) {
            $unit = intval(log($bytes, 1024));
            $units = ['B', 'kB', 'MB', 'GB'];

            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
        
        return $bytes;
    }
}