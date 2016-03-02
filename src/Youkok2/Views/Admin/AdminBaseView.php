<?php
/*
 * File: Home.php
 * Holds: Admin home view
 * Created: 06.08.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Views\BaseView;
use Youkok2\Models\Me;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Routes;
use Youkok2\Utilities\TemplateHelper;

abstract class AdminBaseView extends BaseView {
    
    /*
     * Admin side menu
     */
    
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
    
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct();
        
        if (Me::isAdmin()) {
            // Set menu
            $this->template->assign('HEADER_MENU', null);
            
            // Set some admin things
            $this->template->assign('ADMIN_HEADING', $this->adminHeading);
            
            // Apply admin related stuff to site data
            $this->addSiteData('view', $this->adminIdentifier);
            $this->addSiteData('admin_view', true);
            
            // Create sidemenu
            $this->createSidemenu();
        }
        else {
            Redirect::send('');
        }
    }
    
    /*
     * Method that creates the side menu
     */
    
    private function createSidemenu() {
        // Build the menu
        $output_menu = [];
        
        foreach ($this->menu as $v) {
            $menu_item = $v;
            
            // Various things
            if ($v['identifier'] == $this->adminIdentifier) {
                $menu_item['active'] = true;
            }
            else {
                $menu_item['active'] = false;
            }
            
            // Get the URL
            $menu_item['url'] = TemplateHelper::url_for($menu_item['identifier']);
            
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
                $menu_item['extra'] = call_user_func('Youkok2\Views\\' . $class . '::adminMenuContent');
            }
            
            // Append the menu item
            $output_menu[] = $menu_item;
        }
        
        // Assign to template
        $this->template->assign('ADMIN_SIDEBAR_MENU', $output_menu);
    }
    
    /*
     * Method to add additional information to the admin menu
     */
    
    public static function adminMenuContent() {
        return '';
    }
}
