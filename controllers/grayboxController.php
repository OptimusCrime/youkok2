<?php
/*
 * File: grayboxController.php
 * Holds: The GrayboxController-class
 * Created: 23.04.14
 * Last updated: 23.04.14
 * Project: Youkok2
 * 
*/

//
// The FlatController class
//

class GrayboxController extends Base {

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Check query
        if ($_GET['q'] == 'graybox/newest') {
        	$this->generateNewest();
        }
        else if ($_GET['q'] == 'graybox/downloads') {
        	$this->generateDownloads();
        }
    }
    
    //
    // Method for generating graybox for newest files
    //
    
    private function generateNewest() {
        // Declear variable for storing content
        $ret = '<ul class="list-group">';
        
        $get_newest = "SELECT id
        FROM archive
        WHERE is_directory = 0
        ORDER BY added DESC
        LIMIT 15";
        
        $get_newest_query = $this->db->query($get_newest);
        while ($row = $get_newest_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->setShouldLoadRoot(true);
            $item->createById($row['id']);

            // Add to collection if new
            $this->collection->add($item);

            // Load item from collection
            $element = $this->collection->get($row['id']);

            // CHeck if element was loaded
            if ($element != null) {
                $element_url = $element->generateUrl($this->paths['download'][0]);
                $ret .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> [<span class="moment-timestamp" style="cursor: help;" title="' . $this->prettifySQLDate($element->getAdded()) . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
            }
        }
        
        // End
        $ret .= '</ul>';
        
        // Echo the pure content
        echo $ret;
    }
    
     //
    // Method for generating graybox for newest downloads
    //
    
    private function generateDownloads() {
        // Declear variable for storing content
        $ret = '<ul class="list-group">';
        
        $get_downloads = "SELECT file, downloaded_time
        FROM download
        ORDER BY id DESC
        LIMIT 15";
        
        $get_downloads_query = $this->db->query($get_downloads);
        while ($row = $get_downloads_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->setShouldLoadRoot(true);
            $item->createById($row['file']);

            // Add to collection if new
            $this->collection->add($item);

            // Load item from collection
            $element = $this->collection->get($row['file']);

            // CHeck if element was loaded
            if ($element != null) {
                $element_url = $element->generateUrl($this->paths['download'][0]);
                $ret .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> [<span class="moment-timestamp" style="cursor: help;" title="' . $this->prettifySQLDate($row['downloaded_time']) . '" data-ts="' . $row['downloaded_time'] . '">Laster...</span>]</li>';
            }
        }
        
        // End
        $ret .= '</ul>';
        
        // Echo the pure content
        echo $ret;
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