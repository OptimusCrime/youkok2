<?php
/*
 * File: Home.php
 * Holds: Admin home view
 * Created: 06.08.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views\Admin;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Utilities;

class Home extends AdminBaseView
{
    
    /*
     * For the menu and such
     */
    
    protected $adminIdentifier = 'admin_home';
    protected $adminHeading = 'Forside';
    protected $adminBreadcrumbs = ['Forside'];
    
    /*
     * Always run the constructor
     */
    
    public function __construct($app) {
        parent::__construct($app);
    }
    
    /*
     * Displaying various admin stuff
    */
    
    public function displayAdminHome() {
        // Get downloads pr. day
        $download_pr_day = '';
        $get_download_pr_day  = "SELECT downloaded_time AS 'date', COUNT(id) AS 'num'" . PHP_EOL;
        $get_download_pr_day .= "FROM download" . PHP_EOL;
        $get_download_pr_day .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_download_pr_day .= "ORDER BY downloaded_time DESC" . PHP_EOL;
        $get_download_pr_day .= "LIMIT 14";
        
        $get_download_pr_day_query = Database::$db->query($get_download_pr_day);
        while ($row = $get_download_pr_day_query->fetch(\PDO::FETCH_ASSOC)) {
            $download_pr_day .= '<li><strong>' . Utilities::prettifySQLDate($row['date'], false) . '</strong>: ';
            $download_pr_day .= number_format($row['num']) . '</li>';
        }
        $this->template->assign('ADMIN_DOWNLOADS_PR_DAY', $download_pr_day);
        
        // Display
        $this->displayAndCleanup('admin/home.tpl');
    }
}
