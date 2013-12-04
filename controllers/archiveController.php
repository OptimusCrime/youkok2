<?php
/*
 * File: archiveController.php
 * Holds: The ArchiveController-class
 * Created: 02.10.13
 * Last updated: 05.12.13
 * Project: Youkok2
 * 
*/

//
// The REST-class doing most of the magic
//

class ArchiveController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths) {
        // Calling Base' constructor
        parent::__construct($paths);
        
        // Reverseurl to get the current location
        $real_url = $_GET['q'];
        if ($real_url == 'arkiv') {
            $real_url = 'arkiv/';
        }
        
        // Get the current parent
        $parent = $this->reverseParent($_GET['q'], true);
        
        // Todo, check if 404
        
        $ret = '';
        // Loading newest files from the system
        $get_current_archive = "SELECT name, url_friendly, is_directory
        FROM archive
        WHERE parent = :parent
        ORDER BY is_directory DESC,
        name ASC";
        
        $get_current_archive_query = $this->db->prepare($get_current_archive);
        $get_current_archive_query->execute(array(':parent' => $parent));
        while ($row = $get_current_archive_query->fetch(PDO::FETCH_ASSOC)) {
            // Build string
            $ret .= '<li><a href="' . (($row['is_directory']) ? $real_url . $row['url_friendly']. '/' : $this->fromArchiveToDownload($real_url . $row['url_friendly'])) . '">' . $row['name'] . (($row['is_directory']) ? ' [Folder]' : '') . '</a></li>';
        }
        
        $this->template->assign('ARCHIVE_DISPLAY', $ret);

        // Kill database-connection and cleanup before displaying
        $this->close();
        
        // Display the template
        $this->template->display('archive.tpl');
    }
}

//
// Loading the class-name dynamically and creating an instance doing our magic
//

// Getting the current file-path
$path = explode('/', __FILE__);

// Including the run-script to execute it all
include_once 'run.php';
?>