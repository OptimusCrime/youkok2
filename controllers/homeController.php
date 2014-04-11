<?php
/*
 * File: homeController.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Last updated: 12.04.14
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
        require_once $this->basePath . '/elements/item.class.php';
        
        // Load newest files
        $this->template->assign('HOME_NEWEST', $this->loadNewest());
        
        // Load most popular files
        $this->template->assign('HOME_MOST_POPULAR', $this->loadMostPopular());
        
        // Check if this user is logged in
        if ($this->user->isLoggedIn()) {
            $this->template->assign('HOME_USER_LATEST', '<li class="list-group-item">Kommer!</li>');
            $this->template->assign('HOME_USER_FAVORITES', '<li class="list-group-item">Kommer!</li>');
        } else {
            $this->template->assign('HOME_USER_LATEST', '<li class="list-group-item"><em>Registrer og/eller logg inn!</em></li>');
            $this->template->assign('HOME_USER_FAVORITES', '<li class="list-group-item"><em>Registrer og/eller logg inn!</em></li>');
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
            $item = new Item($this->collection, $this->db);
            $item->createById($row['id']);

            // Add to collection if new
            $this->collection->addIfDoesNotExist($item);

            // Load item from collection
            $element = $this->collection->get($row['id']);

            // CHeck if element was loaded
            if ($element != null) {
                $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->paths['download'][0]) . '">' . $element->getName() . '</a> @ <a href="#">MFEL1010</a> [Opprettet: ' . number_format($element->getDownloadCount(Item::DOWNLOADS_MONTH)) . ']</li>';
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
        $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
        FROM download d
        WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
        GROUP BY d.file";
        
        $get_most_popular_query = $this->db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->createById($row['id']);

            // Add to collection if new
            $this->collection->addIfDoesNotExist($item);

            // Load item from collection
            $element = $this->collection->get($row['id']);

            // Set downloaded
            $element->setDownloadCount(Item::DOWNLOADS_MONTH, $row['downloaded_times']);

            // CHeck if element was loaded
            if ($element != null) {
                $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->paths['download'][0]) . '">' . $element->getName() . '</a> @ <a href="#">MFEL1010</a> [Nedlastninger: ' . number_format($element->getDownloadCount(Item::DOWNLOADS_MONTH)) . ']</a></li>';
            }
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