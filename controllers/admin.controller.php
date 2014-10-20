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

class AdminController extends Youkok2 {

    //
    // The constructor for this subclass
    //

    public function __construct($routes) {
        // Calling Base' constructor
        parent::__construct($routes);
        
        if ($this->user->isLoggedIn() and $this->user->getId() == 10000) {
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
        
        $get_user_number_query = $this->db->prepare($get_user_number);
        $get_user_number_query->execute();
        $get_user_number_result = $get_user_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load files
        $get_file_number = "SELECT COUNT(id) AS 'antall_filer'
        FROM archive
        WHERE is_directory = 0";
        
        $get_file_number_query = $this->db->prepare($get_file_number);
        $get_file_number_query->execute();
        $get_file_number_result = $get_file_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load downloads
        $get_download_number = "SELECT COUNT(id) AS 'antall_nedlastninger'
        FROM download";
        
        $get_download_number_query = $this->db->prepare($get_download_number);
        $get_download_number_query->execute();
        $get_dowload_number_result = $get_download_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Load downloads past 24 hours
        $get_download_number_last24 = "SELECT COUNT(d.id) AS 'antall_nedlastninger'
        FROM download AS d
        LEFT JOIN archive AS a ON a.id = d.file
        WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 DAY) AND a.is_visible = 1";
        
        $get_download_number_last24_query = $this->db->prepare($get_download_number_last24);
        $get_download_number_last24_query->execute();
        $get_download_number_last24_result = $get_download_number_last24_query->fetch(PDO::FETCH_ASSOC);
        
        // Size of filebase
        $get_size_number = "SELECT SUM(size) AS 'size'
        FROM archive
        WHERE is_directory = 0";
        
        $get_size_number_query = $this->db->prepare($get_size_number);
        $get_size_number_query->execute();
        $get_size_number_result = $get_size_number_query->fetch(PDO::FETCH_ASSOC);
        
        // Total bandwidth
        $total_bandwidth = 0;
        $get_bandwidth_number = "SELECT d.file AS  'id', COUNT(d.id) AS  'downloaded_times', a.size
        FROM download d
        LEFT JOIN archive AS a ON a.id = d.file
        GROUP BY d.file";
        $get_bandwidth_number_query = $this->db->prepare($get_bandwidth_number);
        $get_bandwidth_number_query->execute();
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
        ORDER BY downloaded_time DESC";
        $get_download_pr_day_query = $this->db->prepare($get_download_pr_day);
        $get_download_pr_day_query->execute();
        while ($row = $get_download_pr_day_query->fetch(PDO::FETCH_ASSOC)) {
            $download_pr_day .= '<li><strong>' . $this->utils->prettifySQLDate($row['date'], false) . '</strong>: ' . number_format($row['num']) . '</li>';
        }
        $this->template->assign('ADMIN_DOWNLOADS_PR_DAY', $download_pr_day);
        
        // Display
        $this->displayAndCleanup('admin_home.tpl');
    }
}

//
// Return the class name
//

return 'AdminController';