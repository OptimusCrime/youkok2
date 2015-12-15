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

abstract class AdminBaseView extends BaseView {
    
    /*
     * Admin side menu
     */
    
    private $menu = [
        [
            'identifier' => 'home',
            'text' => 'Forside',
            'icon' => 'home',
            'url' => 'admin',
            'class' => 'Youkok2\\Views\\Admin\\Home',
        ], [
            'identifier' => 'new-files',
            'text' => 'Nye bidrag',
            'icon' => 'upload',
            'url' => 'admin/bidrag',
            'class' => 'Youkok2\\Views\\Admin\\NewFiles',
        ], [
            'identifier' => 'files',
            'text' => 'Filer',
            'icon' => 'sitemap',
            'url' => 'admin/filer',
            'class' => null,
        ], [
            'identifier' => 'statistics',
            'text' => 'Statistikk',
            'icon' => 'bar-chart',
            'url' => 'admin/statistikk',
            'class' => null,
        ], [
            'identifier' => 'diagnostikk',
            'text' => 'Diagnostikk',
            'icon' => 'dashboard',
            'url' => 'admin/diagnostikk',
            'class' => null,
        ], [
            'identifier' => 'logs',
            'text' => 'Logger',
            'icon' => 'database',
            'url' => 'admin/logger',
            'class' => null,
        ], [
            'identifier' => 'scripts',
            'text' => 'Scripts',
            'icon' => 'upload',
            'url' => 'admin/terminal',
            'class' => null,
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
            $this->addSiteData('view', 'admin-' . $this->adminIdentifier);
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
            
            if (isset($menu_item['class']) and $menu_item['class'] != null) {
                $menu_item['extra'] = call_user_func($menu_item['class'] . '::adminMenuContent');
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
