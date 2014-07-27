<?php
/*
 * File: utilities.php
 * Holds: Different minor functions
 * Created: 23.06.14
 * Project: Youkok2
 * 
*/

//
// Different minor functions
//

class Utils {

    //
    // Prettify dates
    //
    
    public function prettifySQLDate($d) {
        $norwegianMonths = array('jan', 'feb', 'mar', 'apr', 'mai', 'jun', 'jul', 'aug', 'sep', 
                                 'okt', 'nov', 'des');
        $split1 = explode(' ', $d);
        $split2 = explode('-', $split1[0]);
        
        return (int) $split2[2] . '. ' . $norwegianMonths[$split2[1] - 1] . ' ' . $split2[0] . ' @ ' . $split1[1];
    }

    //
    // Generic method for generating SEO friendly urls and directory names
    //

    public function generateUrlFriendly($s, $for_url = false) {
        // Replace first here to keep "norwegian" names in a way
        $s = str_replace(array('Æ', 'Ø', 'Å'), array('ae', 'o', 'aa'), $s);
        $s = str_replace(array('æ', 'ø', 'å'), array('ae', 'o', 'aa'), $s);

        // Decide how to deal with spaces
        if ($for_url) {
            $s = str_replace(' ', '-', $s);
        }
        else {
            $s = str_replace(' ', '_', $s);
        }

        $s = preg_replace('![^-_a-z0-9\s]+!', '', strtolower($s));
        
        return $s;
    }
}