<?php
/*
 * File: Contribution.php
 * Holds: Admin view for contributions
 * Created: 15.12.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Contribution extends AdminBaseView {
    
    /*
     * For the menu and such
     */
    
    protected $adminIdentifier = 'admin_contribution';
    protected $adminHeading = 'Nye bidrag';
    protected $adminBreadcrumbs = ['Nye bidrag'];
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
    }
    
    /*
     * Override
     */
    
    public static function adminMenuContent() {
        return '<span class="label label-primary pull-right">0</span>';
    }
    
    /*
     * Display
     */
    
    public function displayAdminContributions() {
        // Display
        $this->displayAndCleanup('admin/empty.tpl');
    }
}