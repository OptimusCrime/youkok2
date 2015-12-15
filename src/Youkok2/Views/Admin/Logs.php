<?php
/*
 * File: Logs.php
 * Holds: Admin view for logs view
 * Created: 16.12.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Logs extends AdminBaseView {
    
    /*
     * For the menu and such
     */
    
    protected $adminMenuIdentifier = 'logs';
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
    }
    
    /*
     * Display
     */
    
    public function displayAdminLogs() {
        // Display
        $this->displayAndCleanup('admin/empty.tpl');
    }
}