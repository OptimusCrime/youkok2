<?php
/*
 * File: Admin.php
 * Holds: Views for the admin's eyes only
 * Created: 06.08.2014
 * Project: Youkok2
 * 
 */

namespace Youkok2\Views;

use Youkok2\Models\Me;
use Youkok2\Utilities\Database;
use Youkok2\Utilities\Redirect;
use Youkok2\Utilities\Utilities;

class Admin extends BaseView {

    /*
     * Constructor
     */

    public function __construct() {
        // Calling Base' constructor
        parent::__construct();
        
        if (Me::isAdmin()) {
            // Set menu
            $this->template->assign('HEADER_MENU', null);
            
            // Fetch data
            $this->displayAdminPage();
        }
        else {
            Redirect::send('');
        }
    }
    
    /*
     * Displaying various admin stuff
    */
    
    private function displayAdminPage() {
        // Load users
        $get_user_number  = "SELECT COUNT(id) AS 'antall_brukere'" . PHP_EOL;
        $get_user_number .= "FROM user";
        
        $get_user_number_query = Database::$db->query($get_user_number);
        $get_user_number_result = $get_user_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number  = "SELECT COUNT(id) AS 'antall_filer'" . PHP_EOL;
        $get_file_number .= "FROM archive" . PHP_EOL;
        $get_file_number .= "WHERE is_directory = 0";
        
        $get_file_number_query = Database::$db->query($get_file_number);
        $get_file_number_result = $get_file_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number  = "SELECT COUNT(id) AS 'antall_nedlastninger'" . PHP_EOL;
        $get_download_number .= "FROM download";
        
        $get_download_number_query = Database::$db->query($get_download_number);
        $get_dowload_number_result = $get_download_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Load downloads past 24 hours
        $get_download_number_last24  = "SELECT COUNT(d.id) AS 'antall_nedlastninger'" . PHP_EOL;
        $get_download_number_last24 .= "FROM download AS d" . PHP_EOL;
        $get_download_number_last24 .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_download_number_last24 .= "WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1";
        
        $get_download_number_last24_query = Database::$db->query($get_download_number_last24);
        $get_download_number_last24_result = $get_download_number_last24_query->fetch(\PDO::FETCH_ASSOC);
        
        // Size of filebase
        $get_size_number  = "SELECT SUM(size) AS 'size'" . PHP_EOL;
        $get_size_number .= "FROM archive" . PHP_EOL;
        $get_size_number .= "WHERE is_directory = 0";
        
        $get_size_number_query = Database::$db->query($get_size_number);
        $get_size_number_result = $get_size_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Total bandwidth
        $total_bandwidth = 0;
        $get_bandwidth_number  = "SELECT d.file AS  'id', COUNT(d.id) AS  'downloaded_times', a.size" . PHP_EOL;
        $get_bandwidth_number .= "FROM download d" . PHP_EOL;
        $get_bandwidth_number .= "LEFT JOIN archive AS a ON a.id = d.file" . PHP_EOL;
        $get_bandwidth_number .= "GROUP BY d.file";
        
        $get_bandwidth_number_query = Database::$db->query($get_bandwidth_number);
        while ($row = $get_bandwidth_number_query->fetch(\PDO::FETCH_ASSOC)) {
            $total_bandwidth += ($row['downloaded_times'] * $row['size']);
        }
        
        // Assign
        $this->template->assign('ADMIN_USERS', number_format($get_user_number_result['antall_brukere']));
        $this->template->assign('ADMIN_FILES', number_format($get_file_number_result['antall_filer']));
        $this->template->assign('ADMIN_DOWNLOADS', number_format($get_dowload_number_result['antall_nedlastninger']));
        $this->template->assign('ADMIN_DOWNLOADS_LAST_24', number_format($get_download_number_last24_result['antall_nedlastninger']));
        $this->template->assign('ADMIN_SIZE', Utilities::prettifyFilesize($get_size_number_result['size']));
        $this->template->assign('ADMIN_BANDWIDTH', Utilities::prettifyFilesize($total_bandwidth));
        
        // Get downloads pr. day
        $download_pr_day = '';
        $get_download_pr_day  = "SELECT downloaded_time AS 'date', COUNT(id) AS 'num'" . PHP_EOL;
        $get_download_pr_day .= "FROM download" . PHP_EOL;
        $get_download_pr_day .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_download_pr_day .= "ORDER BY downloaded_time DESC" . PHP_EOL;
        $get_download_pr_day .= "LIMIT 14";
        
        $get_download_pr_day_query = Database::$db->query($get_download_pr_day);
        while ($row = $get_download_pr_day_query->fetch(\PDO::FETCH_ASSOC)) {
            $download_pr_day .= '<li><strong>' . Utilities::prettifySQLDate($row['date'], false) . '</strong>: ' . number_format($row['num']) . '</li>';
        }
        $this->template->assign('ADMIN_DOWNLOADS_PR_DAY', $download_pr_day);
        
        /*
         * Graph
         */
        
        $graph_data = $this->adminPageGraphs();
        $this->template->assign('ADMIN_GRAPH_DATA', json_encode($graph_data[0]));
        $this->template->assign('ADMIN_GRAPH_DATA_ACC', json_encode($graph_data[1]));
        
        /*
         * System stats
         */
        
        // Courses
        $get_course_number  = "SELECT COUNT(id) AS 'num_courses'" . PHP_EOL;
        $get_course_number .= "FROM archive" . PHP_EOL;
        $get_course_number .= "WHERE is_directory = 1" . PHP_EOL;
        $get_course_number .= "AND url IS NULL" . PHP_EOL;
        $get_course_number .= "AND parent IS NULL" . PHP_EOL;
        $get_course_number .= "AND is_visible = 1";
        
        $get_course_number_query = Database::$db->query($get_course_number);
        $get_course_number_result = $get_course_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Files
        $get_files_number  = "SELECT COUNT(id) AS 'num_files'" . PHP_EOL;
        $get_files_number .= "FROM archive" . PHP_EOL;
        $get_files_number .= "WHERE is_directory = 0" . PHP_EOL;
        $get_files_number .= "AND url IS NULL" . PHP_EOL;
        $get_files_number .= "AND is_visible = 1";
        
        $get_files_number_query = Database::$db->query($get_files_number);
        $get_files_number_result = $get_files_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Links
        $get_links_number  = "SELECT COUNT(id) AS 'num_links'" . PHP_EOL;
        $get_links_number .= "FROM archive" . PHP_EOL;
        $get_links_number .= "WHERE is_directory = 0" . PHP_EOL;
        $get_links_number .= "AND url IS NOT NULL" . PHP_EOL;
        $get_links_number .= "AND is_visible = 1";
        
        $get_links_number_query = Database::$db->query($get_links_number);
        $get_links_number_result = $get_links_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Directories
        $get_dirs_number  = "SELECT COUNT(id) AS 'num_dirs'" . PHP_EOL;
        $get_dirs_number .= "FROM archive" . PHP_EOL;
        $get_dirs_number .= "WHERE is_directory = 1" . PHP_EOL;
        $get_dirs_number .= "AND url IS NULL" . PHP_EOL;
        $get_dirs_number .= "AND parent IS NOT NULL" . PHP_EOL;
        $get_dirs_number .= "AND is_visible = 1";
        
        $get_dirs_number_query = Database::$db->query($get_dirs_number);
        $get_dirs_number_result = $get_dirs_number_query->fetch(\PDO::FETCH_ASSOC);
        
        // Assign
        $this->template->assign('ADMIN_NUM_COURSES', number_format($get_course_number_result['num_courses']));
        $this->template->assign('ADMIN_NUM_FILES', number_format($get_files_number_result['num_files']));
        $this->template->assign('ADMIN_NUM_LINKS', number_format($get_links_number_result['num_links']));
        $this->template->assign('ADMIN_NUM_DIRS', number_format($get_dirs_number_result['num_dirs']));
        
        // Display
        $this->displayAndCleanup('admin/home.tpl');
    }
    
    /*
     * Get graph
     */
    
    private function adminPageGraphs() {
        // Some variables
        $output = [[], []];
        $previous_num = 0;
        
        // The query
        $get_all_downloads  = "SELECT COUNT(id) AS 'num', downloaded_time" . PHP_EOL;
        $get_all_downloads .= "FROM download" . PHP_EOL;
        $get_all_downloads .= "GROUP BY TO_DAYS(downloaded_time)" . PHP_EOL;
        $get_all_downloads .= "ORDER BY downloaded_time ASC";
        
        $get_all_downloads_query = Database::$db->query($get_all_downloads);
        while ($row = $get_all_downloads_query->fetch(\PDO::FETCH_ASSOC)) {
            $previous_num += $row['num'];
            $num_count = $previous_num;
                
            // Split the timestamp
            $ts_split = explode(' ', $row['downloaded_time']);
            $date_split = explode('-', $ts_split[0]);
            $time_split = explode(':', $ts_split[1]);
            
            // The string for Higcharts
            $output[0][] = array('Date.UTC(' . $date_split[0] . ', ' . ($date_split[1] - 1) . ', ' . $date_split[2] . ', ' . $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $row['num']);
            $output[1][] = array('Date.UTC(' . $date_split[0] . ', ' . ($date_split[1] - 1) . ', ' . $date_split[2] . ', ' . $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $num_count);
        }
        
        // Return the series here
        return $output;
    }
}