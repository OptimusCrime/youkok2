<?php
/*
 * File: graybox.controller.php
 * Holds: The GrayboxController-class
 * Created: 23.04.14
 * Project: Youkok2
 * 
*/

//
// The FlatController class
//

class GrayboxController extends Base {

    //
    // Some variables
    //
    
    private $commits = array(
    'Forgot the emails, fuuuck', 'Added homescreen text and stuff', 'Added some stuff to header',
    'Cleanup', 'Added good mails', 'Added very good error message if database-connection goes down',
    'Forgot one stupid line', 'Them dates', 'Ups', 'Changed order of stuff in header',
    'Trying to upload not allowed filetype gies error', 'Fixed accidental error',
    'Cleanup', 'Fixed issue where uploader would reload page too soon, fucking up the uploads',
    'Fixed stupid mistake', 'FUCK', 'Vuupps', 'Unfucking stuff',
    'Removed a string that was not supposted to be there', 'Derp', 'Fixed context menu being fucked',
    'Prettieid modals', 'Minor fixes, overhaul, bugstuff etc', 'Derp', 'Fixed header yet again',
    'Hide stuff that should noe be there', 'Forgot the supid dates again', 'Much prettification',
    'Fixed stuff', 'e.pventdefault ass', 'Fixed header a bit more', 'Fixed w3 validator fuckup',
    'Added theme and began making stuff prettier :)', 'Fixed some of the fuck in the header',
    'Fixed commented out line which broke stuff', 'Fixed stupid error', 'wups', 'Cleaned up a bit',
    'Minor cleanup', 'Fixed supid thingy', 'Removed some anoying stuff',
    'Working on implementing the fileupload-stuff', 'And again', 'Changed dates, because I suck',
    'Deleted empty nameless file', 'Expanding the login-meganicsm', 'Added possibility to log in',
    'Made it possible to star stuff and many fixes and stuff',
    'Fixed stupid error causing downloads to be all messed up :)',
    'Did stuff', 'Added overlay and stuff', 'Added autocomplete n stuff',
    'Adde constants for urls etc (might still be some leftovers', 'Removed old methods from the fucked up past',
    'Reimplemented download and 404 handling :D', 'So much done, awesome souce',
    'Fixed various issues and errors, derp', 'Workig on refractoring the entire thingyy',
    'Moved some files around etc', 'Added bootstrap and a lot of other stuff to the project... WIP so much',
    'Began working on a lot of stuff', 'Added some stuff and made more dyamic', 'Did some changes...');

    //
    // The constructor for this subclass
    //

    public function __construct($paths, $base) {
        // Calling Base' constructor
        parent::__construct($paths, $base);
        
        // Check query
        if ($this->queryGetClean() == 'graybox/newest') {
        	$this->generateNewest();
        }
        else if ($this->queryGetClean() == 'graybox/downloads') {
        	$this->generateDownloads();
        }
        else if ($this->queryGetClean() == 'graybox/numbers') {
        	$this->generateNumbers();
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
    
    //
    // Method for generating grayfor for funny numbers
    //
    
    private function generateNumbers() {
        echo '<p><b>Tilfeldig commit:</b> ' . $this->commits[rand(0, (count($this->commits) - 1))] . '</p>';
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