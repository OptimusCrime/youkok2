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
}