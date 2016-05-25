<?php
/*
 * File: CourseDownloadsController.php
 * Holds: Controller for the model CourseDownloads
 * Created: 14.07.2015
 * Project: Youkok2
 *
 */

namespace Youkok2\Models\Controllers\Cache;

use Youkok2\Models\Controllers\BaseController;

class CourseDownloadsController extends BaseController
{
    
    /*
     * Intervals for the query
     */
    
    public static $timeIntervals = [
        // All
        '',

        // Day
        'WHERE d.downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24)) AND a.pending = 0 AND a.deleted = 0',

        // Week
        'WHERE d.downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24 * 7)) AND a.pending = 0 AND a.deleted = 0',

        // Month
        'WHERE d.downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24 * 30)) AND a.pending = 0 AND a.deleted = 0',

        // Year
        'WHERE d.downloaded_time >= (CURRENT_TIMESTAMP - (60 * 60 * 24 * 365)) AND a.pending = 0 AND a.deleted = 0',
    ];
    
    /*
     * Variables
     */
    
    public static $cacheKey = 'cd';
    
    /*
     * Constructor
     */

    public function __construct($model) {
        parent::__construct($this, $model);
    }
}
