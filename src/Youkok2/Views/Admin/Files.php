<?php
/*
 * File: Files.php
 * Holds: Admin view for files view
 * Created: 16.12.2015
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Files extends AdminBaseView {
    
    /*
     * For the menu and such
     */
    
    protected $adminMenuIdentifier = 'files';
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
    }
    
    /*
     * Display
     */
    
    public function displayAdminFiles() {
        // Display
        $this->displayAndCleanup('admin/empty.tpl');
    }
}