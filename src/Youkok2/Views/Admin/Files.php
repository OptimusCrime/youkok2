<?php
namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class Files extends AdminBaseView
{
    
    protected $adminIdentifier = 'admin_files';
    protected $adminHeading = 'Filer';
    protected $adminBreadcrumbs = ['Filer'];
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        parent::run();

        $this->displayAndCleanup('admin/empty.tpl');
    }
}
