<?php
/*
 * File: homeController.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Last updated: 10.04.14
 * Project: Youkok2
 * 
*/

//
// Displaying the Home screen
//

class HomeController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);

        // Include item class
        require_once $this->base . '/elements/user.class.php';
        
        // Load newest files
        $this->template->assign('HOME_NEWEST', $this->loadNewest());
        
        // Load most popular files
        $this->template->assign('HOME_MOST_POPULAR', $this->loadMostPopular());
        
        // Check if this user is logged in
        if ($this->user->isLoggedIn()) {
            $this->template->assign('HOME_USER_LATEST', 'comming');
        } else {
            $this->template->assign('HOME_USER_LATEST', null);
        }

        // Kill database-connection and cleanup before displaying
        $this->close();
        
        // Display the template
        $this->template->display('index.tpl');
    }
    
    //
    //
    //
    
    private function loadNewest() {
        // Declear variable for storing content
        $ret = '';
        
        // Loading newest files from the system TODO add filter
        $get_newest = "SELECT id, name
        FROM archive
        WHERE is_directory = 0
        ORDER BY added DESC
        LIMIT 0, 20";
        
        $get_newest_query = $this->db->prepare($get_newest);
        $get_newest_query->execute();
        while ($row = $get_newest_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $item = new Item($this->collection);
            $item->createById($row['id']);

            // Add to collection if new
            $collection->addIfDoesNotExist($item);

            // Load item from collection
            $element = $collection->get($row['id']);

            // CHeck if element was loaded
            if ($element != null) {
                $ret .= '<li><a href="' . $element->generateUrl('download') . '">' . $element->getName() . ' [lastet ned: xxx]</a></li>';
            }
        }
        
        // Return the content
        return $ret;
    }
    
    //
    //
    //
    
    private function loadMostPopular() {
        $ret = '';
        
        // Loading newest files from the system
        $get_most_popular = "SELECT id, name, downloaded_times
        FROM archive
        WHERE is_directory = 0
        ORDER BY downloaded_times DESC
        LIMIT 0, 20";
        
        $get_most_popular_query = $this->db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(PDO::FETCH_ASSOC)) {
            // Build string
            $ret .= '<li><a href="' . $this->generateUrlById($row['id'], 'download', false) . '">' . $row['name'] . ' [lastet ned: ' . number_format($row['downloaded_times']) . ' ganger]</a></li>';
        }
        
        return $ret;
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