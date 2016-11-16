<?php
namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class Logs extends AdminBaseView
{
    
    protected $adminIdentifier = 'admin_logs';
    protected $adminHeading = 'Logger';
    protected $adminBreadcrumbs = ['Logger'];
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        parent::run();

        $this->displayAndCleanup('admin/empty.tpl');
    }
}
