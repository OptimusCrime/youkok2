<?php
/*
 * File: homeController.php
 * Holds: The HomeController-class
 * Created: 02.10.13
 * Last updated: 22.04.14
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
        
        // Load newest files
        $this->template->assign('HOME_NEWEST', $this->loadNewest());
        
        // Load most popular files
        $this->template->assign('HOME_MOST_POPULAR', $this->loadMostPopular());
        
        // Check if this user is logged in
        if ($this->user->isLoggedIn()) {
            $this->template->assign('HOME_USER_LATEST', $this->loadLastDownloads());
            $this->template->assign('HOME_USER_FAVORITES', $this->loadFavorites());
        } else {
            $this->template->assign('HOME_USER_LATEST', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
            $this->template->assign('HOME_USER_FAVORITES', '<li class="list-group-item"><em><a href="#" data-toggle="dropdown" class="login-opener">Logg inn</a> eller <a href="registrer">registrer deg</a>.</em></li>');
        }
        
        // Display the template
        $this->displayAndCleanup('index.tpl');
    }
    
    //
    // Method for loading the newest files in the system
    //
    
    private function loadNewest() {
        // Declear variable for storing content
        $ret = '';
        
        // Loading newest files from the system TODO add filter
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
                $root_parent = $element->getRootParent();
                $ret .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> @ ' . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . ' [<span class="moment-timestamp" style="cursor: help;" title="' . $element->getAdded() . '" data-ts="' . $element->getAdded() . '">Laster...</span>]</li>';
            }
        }
        
        // Return the content
        return $ret;
    }
    
    //
    // Method for loading the files with most downloads
    //
    
    private function loadMostPopular() {
        $ret = '';

        // Deltas
        $delta = array(' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 WEEK)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 MONTH)', 
            ' WHERE d.downloaded_time >= DATE_SUB(NOW(), INTERVAL 1 YEAR)', 
            '');

        if ($this->user->isLoggedIn()) {
            $user_delta = $this->user->getMostPopularDelta();
        }
        else {
            if (isset($_SESSION['home_popular'])) {
                $user_delta = $_SESSION['home_popular'];
            }
            else {
                $user_delta = 1;
            }
        }

        // Assign to Smarty
        $this->template->assign('HOME_MOST_POPULAR_DELTA', $user_delta);
        
        // Load most popular files from the system
        $get_most_popular = "SELECT d.file as 'id', COUNT(d.id) as 'downloaded_times'
        FROM download d
        " . $delta[$user_delta] . "
        GROUP BY d.file
        ORDER BY downloaded_times DESC
        LIMIT 15";
        
        $get_most_popular_query = $this->db->prepare($get_most_popular);
        $get_most_popular_query->execute();
        while ($row = $get_most_popular_query->fetch(PDO::FETCH_ASSOC)) {
            // Create new object
            $item = new Item($this->collection, $this->db);
            $item->setShouldLoadRoot(true);
            $item->createById($row['id']);

            // Add to collection if new
            $this->collection->add($item);

            // Load item from collection
            $element = $this->collection->get($row['id']);

            // Set downloaded
            $element->setDownloadCount($user_delta, $row['downloaded_times']);

            // CHeck if element was loaded
            if ($element != null) {
                $element_url = $element->generateUrl($this->paths['download'][0]);
                $root_parent = $element->getRootParent();
                $ret .= '<li class="list-group-item"><a href="' . $element_url . '">' . $element->getName() . '</a> @ ' . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . ' [' . number_format($element->getDownloadCount($user_delta)) . ']</li>';
            }
        }

        // Check if null
        if ($ret == '') {
            $ret = '<li class="list-group-item">Det er visst ingen nedlastninger i dette tidsrommet!</li>';
        }
        
        return $ret;
    }

    //
    // Method for loading user favorites
    //
    
    private function loadFavorites() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_favorites = "SELECT file
        FROM favorite
        WHERE user = :user
        ORDER BY ordering ASC";
        
        $get_favorites_query = $this->db->prepare($get_favorites);
        $get_favorites_query->execute(array(':user' => $this->user->getId()));
        while ($row = $get_favorites_query->fetch(PDO::FETCH_ASSOC)) {
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
                $root_parent = $element->getRootParent();
                if ($element->isDirectory()) {
                    $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->paths['archive'][0]) . '">' . $element->getName() . '</a>' . (($root_parent == null or $element->getId() == $root_parent->getId()) ? '' : ' @ <a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . '</li>';
                }
                else {
                    $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->paths['download'][0]) . '">' . $element->getName() . '</a> @ ' . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . '</li>';
                }
            }
        }
        
        // Check if null
        if ($ret == '') {
            $ret .= '<li class="list-group-item"><em>Du har ingen favoritter...</em></li>';
        }

        // Return the content
        return $ret;
    }

    //
    // Method for loading user latest downloads 
    //

    private function loadLastDownloads() {
        // Declear variable for storing content
        $ret = '';
        
        // Load all favorites
        $get_last_downloads = "SELECT file
        FROM download d
        WHERE user = :user
        AND d.id = (
            SELECT dd.id
            FROM download dd
            WHERE d.file = dd.file
            ORDER BY dd.downloaded_time
            DESC LIMIT 1)
        ORDER BY d.downloaded_time DESC
        LIMIT 15";
        
        $get_last_downloads_query = $this->db->prepare($get_last_downloads);
        $get_last_downloads_query->execute(array(':user' => $this->user->getId()));
        while ($row = $get_last_downloads_query->fetch(PDO::FETCH_ASSOC)) {
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
                $root_parent = $element->getRootParent();
                $ret .= '<li class="list-group-item"><a href="' . $element->generateUrl($this->paths['download'][0]) . '">' . $element->getName() . '</a> @ ' . ($root_parent == null ? '' : '<a href="' . $root_parent->generateUrl($this->paths['archive'][0]) . '">' . $root_parent->getName() . '</a>') . '</li>';
            }
        }
        
        // Check if null
        if ($ret == '') {
            $ret .= '<li class="list-group-item"><em>Du har ikke lastet ned noen filer enda...</em></li>';
        }

        // Return the content
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