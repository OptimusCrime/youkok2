<?php
/*
 * File: Statistics.php
 * Holds: Admin view for statistics view
 * Created: 16.12.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Statistics extends AdminBaseView {
    
    /*
     * For the menu and such
     */
    
    protected $adminIdentifier = 'admin_statistics';
    protected $adminHeading = 'Statistikk';
    protected $adminBreadcrumbs = ['Statistikk'];
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
    }
    
    /*
     * Display
     */
    
    public function displayAdminStatistics() {
        // Display
        $this->displayAndCleanup('admin/empty.tpl');
    }
}