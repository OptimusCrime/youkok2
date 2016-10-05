<?php
namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class Contribution extends AdminBaseView
{
    
    protected $adminIdentifier = 'admin_contribution';
    protected $adminHeading = 'Nye bidrag';
    protected $adminBreadcrumbs = ['Nye bidrag'];
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    public static function adminMenuContent() {
        return '<span class="label label-primary pull-right">0</span>';
    }

    public function run() {
        parent::run();

        $this->displayAndCleanup('admin/empty.tpl');
    }
}
