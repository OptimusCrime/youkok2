<?php
/*
 * File: admin.controller.php
 * Holds: The AdminController-class
 * Created: 06.08.14
 * Project: Youkok2
 * 
*/

//
// The AdminController class
//

class AdminController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        if ($this->user->isAdmin()) {
            $this->displayAdminPage();
        }
        else {
            $this->redirect('');
        }
    }
    
    //
    // Displaying various admin stuff
    //
    
    private function displayAdminPage() {
        // Load users
        $get_user_number = "SELECT COUNT(id) AS 'antall_brukere'
        FROM user";
        
        $get_user_number_query->query($get_user_number);
        $get_user_number_result = $get_user_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number = "SELECT COUNT(id) AS 'antall_filer'
        FROM archive
        WHERE is_directory = 0";
        
        $get_file_number_query->query($get_file_number);
        $get_file_number_result = $get_file_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number = "SELECT COUNT(id) AS 'antall_nedlastninger'
        FROM download";
        
        $get_download_number_query->query($get_download_number);
        $get_dowload_number_result = $get_download_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load downloads past 24 hours
        $get_download_number_last24 = "SELECT COUNT(d.id) AS 'antall_nedlastninger'
        FROM download AS d
        LEFT JOIN archive AS a ON a.id = d.file
        WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1";
        
        $get_download_number_last24_query->query($get_download_number_last24);
        $get_download_number_last24_result = $get_download_number_last24_query->fetch(PDO::FETCH_ASSOC);
        
        // Size of filebase
        $get_size_number = "SELECT SUM(size) AS 'size'
        FROM archive
        WHERE is_directory = 0";
        
        $get_size_number_query->query($get_size_number);
        $get_size_number_result = $get_size_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Total bandwidth
        $total_bandwidth = 0;
        $get_bandwidth_number = "SELECT d.file AS  'id', COUNT(d.id) AS  'downloaded_times', a.size
        FROM download d
        LEFT JOIN archive AS a ON a.id = d.file
        GROUP BY d.file";
        
        $get_bandwidth_number_query->query($get_bandwidth_number);
        while ($row = $get_bandwidth_number_query->fetch(PDO::FETCH_ASSOC)) {
            $total_bandwidth += ($row['downloaded_times'] * $row['size']);
        }
        
        // Assign
        $this->template->assign('ADMIN_USERS', number_format($get_user_number_result['antall_brukere']));
        $this->template->assign('ADMIN_FILES', number_format($get_file_number_result['antall_filer']));
        $this->template->assign('ADMIN_DOWNLOADS', number_format($get_dowload_number_result['antall_nedlastninger']));
        $this->template->assign('ADMIN_DOWNLOADS_LAST_24', number_format($get_download_number_last24_result['antall_nedlastninger']));
        $this->template->assign('ADMIN_SIZE', $this->utils->prettifyFilesize($get_size_number_result['size']));
        $this->template->assign('ADMIN_BANDWIDTH', $this->utils->prettifyFilesize($total_bandwidth));
        
        // Get downloads pr. day
        $download_pr_day = '';
        $get_download_pr_day = "SELECT downloaded_time AS 'date', COUNT(id) AS 'num' 
        FROM download 
        GROUP BY TO_DAYS(downloaded_time) 
        ORDER BY downloaded_time DESC
        LIMIT 14";
        
        $get_download_pr_day_query->query($get_download_pr_day);
        while ($row = $get_download_pr_day_query->fetch(PDO::FETCH_ASSOC)) {
            $download_pr_day .= '<li><strong>' . $this->utils->prettifySQLDate($row['date'], false) . '</strong>: ' . number_format($row['num']) . '</li>';
        }
        $this->template->assign('ADMIN_DOWNLOADS_PR_DAY', $download_pr_day);
        
        //
        // Get graphs
        //
        
        $graph_data = $this->adminPageGraphs();
        $this->template->assign('ADMIN_GRAPH_DATA', json_encode($graph_data[0]));
        $this->template->assign('ADMIN_GRAPH_DATA_ACC', json_encode($graph_data[1]));
        
        //
        // Get system stats
        //
        
        // Courses
        $get_course_number = "SELECT COUNT(id) AS 'num_couses'
        FROM course";
        
        $get_course_number_query->query($get_course_number);
        $get_course_number_result = $get_course_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Files
        $get_files_number = "SELECT COUNT(id) AS 'num_files'
        FROM archive
        WHERE is_directory = 0 
        AND url IS NULL 
        AND is_visible = 1";
        
        $get_files_number_query->query($get_files_number);
        $get_files_number_result = $get_files_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Links
        $get_links_number = "SELECT COUNT(id) AS 'num_links'
        FROM archive
        WHERE is_directory = 0 
        AND url IS NOT NULL 
        AND is_visible = 1";
        
        $get_links_number_query->query($get_links_number);
        $get_links_number_result = $get_links_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Directories
        $get_dirs_number = "SELECT COUNT(id) AS 'num_dirs'
        FROM archive
        WHERE is_directory = 1
        AND url IS NULL 
        AND is_visible = 1";
        
        $get_dirs_number_query->query($get_dirs_number);
        $get_dirs_number_result = $get_dirs_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Assign
        $this->template->assign('ADMIN_NUM_COURSES', number_format($get_course_number_result['num_couses']));
        $this->template->assign('ADMIN_NUM_FILES', number_format($get_files_number_result['num_files']));
        $this->template->assign('ADMIN_NUM_LINKS', number_format($get_links_number_result['num_links']));
        $this->template->assign('ADMIN_NUM_DIRS', number_format($get_dirs_number_result['num_dirs']));
        
        // Display
        $this->displayAndCleanup('admin_home.tpl');
    }
    
    //
    // Get graphs
    //
    
    private function adminPageGraphs() {
        // Some variables
        $output = [[], []];
        $previous_num = 0;
        
        // The query
        $get_all_downloads = "SELECT COUNT(id) AS 'num', downloaded_time
        FROM download
        GROUP BY TO_DAYS(downloaded_time)
        ORDER BY downloaded_time ASC";
        
        $get_all_downloads_query->query($get_all_downloads);
        while ($row = $get_all_downloads_query->fetch(PDO::FETCH_ASSOC)) {
            $previous_num += $row['num'];
            $num_count = $previous_num;
                
            // Split the timestamp
            $ts_split = explode(' ', $row['downloaded_time']);
            $date_split = explode('-', $ts_split[0]);
            $time_split = explode(':', $ts_split[1]);
            
            // The string for Higcharts
            $output[0][] = array('Date.UTC(' . $date_split[0] . ', ' . $date_split[1] . ', ' . $date_split[2] . ', ' . $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $row['num']);
            $output[1][] = array('Date.UTC(' . $date_split[0] . ', ' . $date_split[1] . ', ' . $date_split[2] . ', ' . $time_split[0] . ', ' . $time_split[1] . ', ' . $time_split[2] . ')',
                              $num_count);
        }
        
        // Return the series here
        return $output;
    }
}

//
// Return the class name
//

return 'AdminController';