<?php
namespace Youkok2\Views\Admin;

use Youkok2\Views\BaseView;
use Youkok2\Models\Me;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\TemplateHelper;

abstract class AdminBaseView extends BaseView
{
    
    private $menu = [
        [
            'identifier' => 'admin_home',
            'text' => 'Forside',
            'icon' => 'home',
        ], [
            'identifier' => 'admin_contribution',
            'text' => 'Nye bidrag',
            'icon' => 'upload',
        ], [
            'identifier' => 'admin_files',
            'text' => 'Filer',
            'icon' => 'sitemap',
        ], [
            'identifier' => 'admin_statistics',
            'text' => 'Statistikk',
            'icon' => 'bar-chart',
        ], [
            'identifier' => 'admin_diagnostics',
            'text' => 'Diagnostikk',
            'icon' => 'dashboard',
        ], [
            'identifier' => 'admin_logs',
            'text' => 'Logger',
            'icon' => 'database',
        ], [
            'identifier' => 'admin_scripts',
            'text' => 'Scripts',
            'icon' => 'upload',
        ]
    ];
    
    public function __construct($app) {
        parent::__construct($app);
    }

    public function run() {
        if ($this->me->isAdmin()) {
            $this->template->assign('HEADER_MENU', null);
            $this->template->assign('ADMIN_HEADING', $this->adminHeading);

            $this->addSiteData('view', $this->adminIdentifier);
            $this->addSiteData('admin_view', true);

            $this->createSidemenu();
        }
        else {
            $this->application->send('');
        }
    }
    
    private function createSidemenu() {
        $output_menu = [];
        
        foreach ($this->menu as $v) {
            $menu_item = $v;
            
            if ($v['identifier'] == $this->adminIdentifier) {
                $menu_item['active'] = true;
            }
            else {
                $menu_item['active'] = false;
            }
            
            $menu_item['url'] = TemplateHelper::urlFor($menu_item['identifier']);
            
            // Get the correct class
            $class = null;
            $routes = Routes::getRoutes();
            foreach ($routes as $view => $list) {
                foreach ($list as $v) {
                    if (isset($v['identifier']) and $v['identifier'] == $menu_item['identifier']) {
                        $class = $view;
                        break;
                    }
                }
            }
            
            if ($class !== null) {
                $menu_item['extra'] = call_user_func('Youkok2\\' . $class . '::adminMenuContent');
            }
            
            $output_menu[] = $menu_item;
        }
        
        $this->template->assign('ADMIN_SIDEBAR_MENU', $output_menu);
    }
    
    public static function adminMenuContent() {
        return '';
    }
}