<?php
/*
 * File: Scripts.php
 * Holds: Admin view for scripts view
 * Created: 15.12.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Scripts extends AdminBaseView {
    
    /*
     * For the menu and such
     */
    
    protected $adminIdentifier = 'admin_scripts';
    protected $adminHeading = 'Scripts';
    protected $adminBreadcrumbs = ['Scripts'];
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
    }
    
    /*
     * Display
     */
    
    public function displayAdminScripts() {
        // Display
        $this->displayAndCleanup('admin/empty.tpl');
    }
}